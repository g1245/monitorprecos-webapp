<?php

namespace App\Services\Awin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Csv\Reader;
use League\Csv\Statement;

class CsvImportService
{
    public const CHUNK_SIZE = 500;

    /**
     * Column renames to strip special characters from AWIN feed headers.
     */
    private const COLUMN_RENAMES = [
        'Fashion:suitable_for' => 'fashion_suitable_for',
        'Fashion:category'     => 'fashion_category',
        'Fashion:size'         => 'fashion_size',
        'Fashion:material'     => 'fashion_material',
        'Fashion:pattern'      => 'fashion_pattern',
        'Fashion:swatch'       => 'fashion_swatch',
    ];

    /**
     * Prepare the import: rename the CSV, read headers and create the destination table.
     *
     * Returns metadata consumed by subsequent chunk jobs:
     *  - safe_csv_path  : relative path after the ok_ rename
     *  - table_name     : generated table name
     *  - headers        : renamed column headers (original order)
     *  - valid_headers  : sanitized snake_case column names (same order)
     *
     * @param  string  $csvRelativePath   Path relative to the local storage disk.
     * @param  string  $storeInternalName
     * @return array{safe_csv_path: string, table_name: string, headers: array<int,string>, valid_headers: array<int,string>}
     *
     * @throws \RuntimeException
     */
    public function prepare(string $csvRelativePath, string $storeInternalName): array
    {
        $absolutePath = Storage::disk('local')->path($csvRelativePath);

        $this->renameFileForSafety($absolutePath);

        $safeRelativePath  = dirname($csvRelativePath) . '/ok_' . basename($csvRelativePath);
        $safeAbsolutePath  = Storage::disk('local')->path($safeRelativePath);

        $csv = Reader::createFromPath($safeAbsolutePath, 'r');
        $csv->setHeaderOffset(0);

        $headers      = $this->renameColumns($csv->getHeader());
        $validHeaders = $this->sanitizeColumnNames($headers);
        $tableName    = $this->generateTableName($storeInternalName);

        $this->createTable($tableName, $validHeaders);

        return [
            'safe_csv_path' => $safeRelativePath,
            'table_name'    => $tableName,
            'headers'       => $headers,
            'valid_headers' => $validHeaders,
        ];
    }

    /**
     * Import one chunk of rows (CHUNK_SIZE) into an existing table.
     *
     * Uses League\Csv Statement to seek directly to $offset without loading the
     * full file into memory. Returns the number of raw CSV rows read; if that
     * number is less than CHUNK_SIZE the caller knows the file is exhausted.
     *
     * @param  string            $safeCsvRelativePath  Already-renamed (ok_*) path.
     * @param  string            $tableName
     * @param  array<int,string> $headers              Renamed headers (original order).
     * @param  array<int,string> $validHeaders         Sanitized column names (same order).
     * @param  int               $offset               Zero-based data-row offset to start from.
     * @return int               Number of CSV rows actually read.
     */
    public function importChunk(
        string $safeCsvRelativePath,
        string $tableName,
        array $headers,
        array $validHeaders,
        int $offset,
    ): int {
        $absolutePath = Storage::disk('local')->path($safeCsvRelativePath);

        $csv = Reader::createFromPath($absolutePath, 'r');
        $csv->setHeaderOffset(0);

        $stmt    = Statement::create()->offset($offset)->limit(self::CHUNK_SIZE);
        $records = $stmt->process($csv);

        // count() on ResultSet is O(1) — more reliable than manual iteration count.
        $rowsRead = count($records);
        $chunk    = [];

        foreach ($records->getRecords($headers) as $record) {
            Log::channel('awin')->info('Row from CSV chunk', [
                'table' => $tableName,
                'offset' => $offset,
                'row' => $record,
                'rows_read' => $rowsRead,
            ]);
            
            $merchantProductId = trim($record['merchant_product_id'] ?? '');

            if (empty($merchantProductId)) {
                continue;
            }

            $sanitized = [];

            foreach ($validHeaders as $index => $col) {
                $originalKey     = $headers[$index] ?? $col;
                $sanitized[$col] = isset($record[$originalKey]) ? (string) $record[$originalKey] : null;
            }

            $chunk[] = $sanitized;
        }

        if (!empty($chunk)) {
            $updateColumns = array_values(array_diff($validHeaders, ['merchant_product_id']));
            DB::table($tableName)->upsert($chunk, ['merchant_product_id'], $updateColumns);
        }

        Log::channel('awin')->info('CSV chunk upserted', [
            'table'         => $tableName,
            'offset'        => $offset,
            'rows_read'     => $rowsRead,
            'rows_upserted' => count($chunk),
        ]);

        return $rowsRead;
    }

    /**
     * Import a raw CSV feed into a dynamically created table.
     *
     * @param  string  $csvRelativePath  Path relative to the local storage disk.
     * @param  string  $storeInternalName
     * @return string  The created table name.
     *
     * @throws \RuntimeException
     */
    public function import(string $csvRelativePath, string $storeInternalName): string
    {
        $absolutePath = Storage::disk('local')->path($csvRelativePath);

        $this->renameFileForSafety($absolutePath);

        $safeAbsolutePath = dirname($absolutePath) . '/ok_' . basename($absolutePath);

        $csv = Reader::createFromPath($safeAbsolutePath, 'r');
        $csv->setHeaderOffset(0);

        $headers = $csv->getHeader();
        $headers = $this->renameColumns($headers);

        $validHeaders = $this->sanitizeColumnNames($headers);

        $tableName = $this->generateTableName($storeInternalName);

        $this->createTable($tableName, $validHeaders);

        $rowsInserted = 0;
        $chunk = [];
        $updateColumns = array_values(array_diff($validHeaders, ['merchant_product_id']));

        foreach ($csv->getRecords($headers) as $record) {
            $merchantProductId = trim($record['merchant_product_id'] ?? '');

            if (empty($merchantProductId)) {
                continue;
            }

            $sanitized = [];

            foreach ($validHeaders as $index => $col) {
                $originalKey = $headers[$index] ?? $col;
                $sanitized[$col] = isset($record[$originalKey]) ? (string) $record[$originalKey] : null;
            }

            $chunk[] = $sanitized;

            if (count($chunk) >= self::CHUNK_SIZE) {
                DB::table($tableName)->upsert($chunk, ['merchant_product_id'], $updateColumns);
                $rowsInserted += count($chunk);
                $chunk = [];
            }
        }

        if (!empty($chunk)) {
            DB::table($tableName)->upsert($chunk, ['merchant_product_id'], $updateColumns);
            $rowsInserted += count($chunk);
        }

        Log::channel('awin')->info('CSV import completed', [
            'table' => $tableName,
            'rows' => $rowsInserted,
            'file' => basename($safeAbsolutePath),
        ]);

        return $tableName;
    }

    /**
     * Rename the CSV file to ok_{name} before processing to prevent reprocessing on crash.
     */
    private function renameFileForSafety(string $absolutePath): void
    {
        $dir = dirname($absolutePath);
        $basename = basename($absolutePath);

        if (str_starts_with($basename, 'ok_')) {
            return;
        }

        $newPath = $dir . '/ok_' . $basename;

        if (!rename($absolutePath, $newPath)) {
            throw new \RuntimeException("Unable to rename CSV file for safety: {$absolutePath}");
        }
    }

    /**
     * Apply column renames for Fashion:* and other special header names.
     *
     * @param  array<int, string>  $headers
     * @return array<int, string>
     */
    private function renameColumns(array $headers): array
    {
        return array_map(function (string $header) {
            if (isset(self::COLUMN_RENAMES[$header])) {
                return self::COLUMN_RENAMES[$header];
            }

            // GroupBuying:* → groupbuying_*
            if (str_starts_with($header, 'GroupBuying:')) {
                return 'groupbuying_' . Str::snake(substr($header, 12));
            }

            // ShoppingNL:* → shoppingnl_*
            if (str_starts_with($header, 'ShoppingNL:')) {
                return 'shoppingnl_' . Str::snake(substr($header, 11));
            }

            return $header;
        }, $headers);
    }

    /**
     * Convert column names to safe snake_case identifiers.
     *
     * @param  array<int, string>  $headers
     * @return array<int, string>
     */
    private function sanitizeColumnNames(array $headers): array
    {
        return array_map(fn(string $h) => Str::snake(preg_replace('/[^a-zA-Z0-9_]/', '_', $h)), $headers);
    }

    /**
     * Generate the stable table name for a store's import: import_{store}.
     */
    private function generateTableName(string $storeInternalName): string
    {
        $slug = Str::snake(preg_replace('/[^a-zA-Z0-9_]/', '_', $storeInternalName));

        return "import_{$slug}";
    }

    /**
     * Dynamically create a schema-free table with all columns as TEXT.
     *
     * @param  string          $tableName
     * @param  array<int, string>  $columns
     */
    private function createTable(string $tableName, array $columns): void
    {
        if (Schema::hasTable($tableName)) {
            return;
        }

        Schema::create($tableName, function (\Illuminate\Database\Schema\Blueprint $table) use ($columns) {
            $table->id();

            foreach ($columns as $column) {
                if ($column === 'merchant_product_id') {
                    $table->string($column, 255)->nullable();
                } else {
                    $table->text($column)->nullable();
                }
            }

            $table->unique('merchant_product_id');
            $table->timestamps();
        });
    }
}

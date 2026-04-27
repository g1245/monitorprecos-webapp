<?php

namespace App\Services\Awin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Csv\Reader;

class CsvImportService
{
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

        $headers      = $this->renameColumns($csv->getHeader());
        $validHeaders = $this->sanitizeColumnNames($headers);
        $tableName    = $this->generateTableName($storeInternalName);
        $updateColumns = array_values(array_diff($validHeaders, ['merchant_product_id']));

        $this->createTable($tableName, $validHeaders);

        $rows = [];

        foreach ($csv->getRecords($headers) as $record) {
            $merchantProductId = trim($record['merchant_product_id'] ?? '');

            if (empty($merchantProductId)) {
                continue;
            }

            $sanitized = [];

            foreach ($validHeaders as $colIndex => $col) {
                $originalKey     = $headers[$colIndex] ?? $col;
                $sanitized[$col] = isset($record[$originalKey]) ? (string) $record[$originalKey] : null;
            }

            $rows[] = $sanitized;
        }

        if (!empty($rows)) {
            DB::table($tableName)->upsert($rows, ['merchant_product_id'], $updateColumns);
        }

        Log::channel('awin')->info('CSV import completed', [
            'table' => $tableName,
            'rows'  => count($rows),
            'file'  => basename($safeAbsolutePath),
        ]);

        return $tableName;
    }

    /**
     * Rename the CSV file to ok_{name} before processing to prevent reprocessing on crash.
     */
    private function renameFileForSafety(string $absolutePath): void
    {
        $dir      = dirname($absolutePath);
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

            if (str_starts_with($header, 'GroupBuying:')) {
                return 'groupbuying_' . Str::snake(substr($header, 12));
            }

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
     * @param  string              $tableName
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

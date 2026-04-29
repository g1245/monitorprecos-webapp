<?php

namespace App\Console\Commands\Product;

use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Console\Command;

class FillMerchantCategoryFromAttributesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fill-merchant-category-from-attributes
                            {--store_id= : Process only products belonging to this store ID}
                            {--dry-run : Show what would be updated without persisting changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill merchant_category, merchant_category_1, merchant_category_2 and merchant_category_3 from ProductAttributes for products that have empty merchant_category';

    /**
     * Separator used to split merchant_category into numbered parts.
     */
    private const CATEGORY_SEPARATOR = '>';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $storeIdOption = $this->option('store_id');
        $dryRun        = (bool) $this->option('dry-run');
        $storeId       = null;

        if ($storeIdOption !== null) {
            $storeId = (int) $storeIdOption;

            if ($storeId <= 0) {
                $this->error('The --store_id option must be a positive integer.');

                return Command::FAILURE;
            }
        }

        $query = Product::query()
            ->where(function ($q) {
                $q->whereNull('merchant_category')
                    ->orWhere('merchant_category', '');
            })
            ->when($storeId !== null, fn ($q) => $q->where('store_id', $storeId));

        $total = (clone $query)->count();

        if ($total === 0) {
            $this->info('No products with empty merchant_category found.');

            return Command::SUCCESS;
        }

        $scope = $storeId !== null ? "store ID {$storeId}" : 'all stores';
        $mode  = $dryRun ? ' [DRY RUN]' : '';

        $this->info("Processing {$total} product(s) with empty merchant_category [{$scope}]{$mode}...");

        $progressBar = $this->output->createProgressBar($total);
        $progressBar->start();

        $updated = 0;
        $skipped = 0;

        $query->chunkById(200, function ($products) use (&$updated, &$skipped, $dryRun, $progressBar) {
            $productIds = $products->pluck('id')->all();

            $attributes = ProductAttribute::query()
                ->whereIn('product_id', $productIds)
                ->where('key', 'merchant_category')
                ->get()
                ->keyBy('product_id');

            foreach ($products as $product) {
                $attr = $attributes->get($product->id);

                if ($attr === null || empty($attr->description)) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                $parts = array_map(
                    'trim',
                    explode(self::CATEGORY_SEPARATOR, $attr->description)
                );

                $values = [
                    'merchant_category'   => $attr->description,
                    'merchant_category_1' => $parts[0] ?? null,
                    'merchant_category_2' => $parts[1] ?? null,
                    'merchant_category_3' => $parts[2] ?? null,
                ];

                if (!$dryRun) {
                    $product->update($values);
                }

                $updated++;
                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->newLine();

        $this->info("Done. Updated: {$updated} | Skipped (no attributes): {$skipped}");

        return Command::SUCCESS;
    }
}

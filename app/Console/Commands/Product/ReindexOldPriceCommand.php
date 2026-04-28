<?php

namespace App\Console\Commands\Product;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReindexOldPriceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reindex-old-price
                            {--store_id= : ID da loja para filtrar produtos (opcional)}
                            {--product_id= : ID do produto para reindexar individualmente (opcional)}
                            {--days=2 : Janela de dias para inspecionar o histórico de preços (padrão: 2)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reindexar old_price dos produtos com base no histórico de preços. Quando nenhum filtro é informado, processa todas as lojas com catálogo ativo.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $storeIdOption = $this->option('store_id');
        $productIdOption = $this->option('product_id');
        $daysOption = $this->option('days');
        $productId = null;
        $storeId = null;
        $days = (int) $daysOption;

        if ($days <= 0) {
            $this->error('A opção --days deve ser um número inteiro positivo.');

            return Command::FAILURE;
        }

        if ($storeIdOption !== null) {
            $storeId = (int) $storeIdOption;

            if ($storeId <= 0) {
                $this->error('A opção --store_id deve ser um número inteiro positivo quando informada.');

                return Command::FAILURE;
            }
        }

        if ($productIdOption !== null) {
            $productId = (int) $productIdOption;

            if ($productId <= 0) {
                $this->error('A opção --product_id deve ser um número inteiro positivo quando informada.');

                return Command::FAILURE;
            }
        }

        $productsQuery = Product::query()
            ->when($storeId !== null, fn($query) => $query->where('store_id', $storeId))
            ->when($productId !== null, fn($query) => $query->whereKey($productId))
            ->when($storeId === null && $productId === null, function ($query): void {
                $activeStoreIds = Store::where('has_public', true)->pluck('id');
                $query->whereIn('store_id', $activeStoreIds);
            });

        $scope = match (true) {
            $productId !== null => "produto #{$productId}",
            $storeId !== null   => "loja #{$storeId}",
            default             => 'todas as lojas com catálogo ativo',
        };

        $this->line("Escopo: <info>{$scope}</info> | Janela de histórico: <info>{$days} dia(s)</info>");

        $totalProducts = (clone $productsQuery)->count();

        if ($totalProducts === 0) {
            $this->warn('Nenhum produto encontrado para os parâmetros informados.');

            return Command::SUCCESS;
        }

        $this->line("Total de produtos encontrados: <info>{$totalProducts}</info>");

        $this->line('Iniciando reindexação...');

        $processed = 0;
        $updated = 0;

        $bar = $this->output->createProgressBar($totalProducts);
        $bar->start();

        $productsQuery
            ->orderBy('id')
            ->chunkById(200, function ($products) use (&$processed, &$updated, $days, $bar): void {
                foreach ($products as $product) {
                    $processed++;

                    $historyRecord = $product->priceHistories()
                        ->where('created_at', '>=', now()->subDays($days))
                        ->where('price', '>', $product->price)
                        ->orderByDesc('created_at')
                        ->orderByDesc('id')
                        ->first(['price', 'created_at']);

                    if ($historyRecord !== null) {
                        $newOldPrice = number_format((float) $historyRecord->price, 4, '.', '');

                        DB::table('products')
                            ->where('id', $product->id)
                            ->update([
                                'old_price'    => $newOldPrice,
                                'old_price_at' => $historyRecord->created_at,
                            ]);

                        $updated++;
                    } else {
                        DB::table('products')
                            ->where('id', $product->id)
                            ->update([
                                'old_price'    => null,
                                'old_price_at' => null,
                            ]);
                    }

                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine();

        $skipped = $processed - $updated;

        $this->info("Reindexação concluída com sucesso.");
        $this->line("  Processados : <info>{$processed}</info>");
        $this->line("  Atualizados : <info>{$updated}</info>");
        $this->line("  Sem histórico relevante: <comment>{$skipped}</comment>");

        return Command::SUCCESS;
    }
}

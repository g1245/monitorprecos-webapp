<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Jobs\ReindexPricesJob;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reindexPrices')
                ->label('Reindexar Preços')
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Reindexar Preços')
                ->modalDescription('Isso irá disparar a reindexação de old_price para todos os produtos em segundo plano. Deseja continuar?')
                ->modalSubmitActionLabel('Sim, reindexar')
                ->action(function (): void {
                    ReindexPricesJob::dispatch();

                    Notification::make()
                        ->title('Reindexação de preços agendada')
                        ->body('O job de reindexação de preços foi disparado com sucesso.')
                        ->success()
                        ->send();
                }),

            CreateAction::make(),
        ];
    }
}

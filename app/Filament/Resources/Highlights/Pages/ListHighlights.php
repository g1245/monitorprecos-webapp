<?php

namespace App\Filament\Resources\Highlights\Pages;

use App\Filament\Resources\Highlights\HighlightResource;
use App\Jobs\ReindexTopOffersJob;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListHighlights extends ListRecords
{
    protected static string $resource = HighlightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reindexTopOffers')
                ->label('Reindexar Principais Ofertas')
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Reindexar Principais Ofertas')
                ->modalDescription('Isso irá disparar a sincronização dos produtos com maiores descontos em segundo plano. Deseja continuar?')
                ->modalSubmitActionLabel('Sim, reindexar')
                ->action(function (): void {
                    ReindexTopOffersJob::dispatch();

                    Notification::make()
                        ->title('Reindexação de principais ofertas agendada')
                        ->body('O job de reindexação das principais ofertas foi disparado com sucesso.')
                        ->success()
                        ->send();
                }),

            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('store.name')
                    ->label('Store')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Price')
                    ->formatStateUsing(function ($state) {
                        return 'R$ ' . number_format($state, 2, ',', '.');
                    })
                    ->sortable(),
                TextColumn::make('old_price')
                    ->label('Old Price')
                    ->formatStateUsing(fn ($state) => $state ? 'R$ ' . number_format($state, 2, ',', '.') : '-')
                    ->sortable(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('departments.name')
                    ->label('Departamentos')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList(),
                TextColumn::make('created_at')
                    ->dateTime(),
                IconColumn::make('in_stock')
                    ->label('Em estoque')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('store')
                    ->relationship('store', 'name')
                    ->label('Store'),
                Filter::make('recent_discount')
                    ->label('Alteração de preço recente')
                    ->query(fn ($query) => $query->withRecentPriceChange(3)),
                Filter::make('out_of_stock')
                    ->label('Fora de estoque')
                    ->query(fn ($query) => $query->where('in_stock', false)),
            ])
            ->recordActions([
                Action::make('view_product')
                    ->label('View Product')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('product.show', ['id' => $record->id, 'slug' => Str::of($record->name)->slug()]))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

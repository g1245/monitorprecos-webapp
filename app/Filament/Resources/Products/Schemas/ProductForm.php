<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('store_id')
                    ->relationship('store', 'name')
                    ->required(),

                TextInput::make('name')
                    ->columnSpanFull()
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('R$'),
                TextInput::make('sku')
                    ->label('SKU')
                    ->default(null),
                TextInput::make('brand')
                    ->default(null),
                
                Select::make('departments')
                    ->relationship('departments', 'name')
                    ->multiple()
                    ->preload()
                    ->columnSpanFull()
                    ->label('Departamentos'),
                
                TextInput::make('image_url')
                    ->label('Image URL')
                    ->url()
                    ->default(null)
                    ->reactive(),
                
                Placeholder::make('image_preview')
                    ->label('Preview')
                    ->content(function ($get) {
                        $url = $get('image_url');
                        if ($url) {
                            return '<a href="' . htmlspecialchars($url) . '" target="_blank" rel="noopener noreferrer">View Image</a>';
                        }
                        return 'No image URL provided.';
                    }),
                
                Textarea::make('deep_link')
                    ->default(null),
                Textarea::make('external_link')
                    ->default(null),
                Toggle::make('in_stock')
                    ->label('Em estoque')
                    ->default(true),
            ]);
    }
}

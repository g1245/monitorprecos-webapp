<?php

namespace App\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('parent_id')
                    ->relationship('parent', 'name')
                    ->default(null),
                Toggle::make('show_in_menu')
                    ->label('Exibir no menu')
                    ->default(true),
            ]);
    }
}

<?php

namespace App\Filament\Pages\Reports;

use App\Models\UserBrowsingHistory;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\TextInput;

class ProductViewsReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.reports.product-views-report';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Views de Produtos';

    protected static ?string $title = 'Relatório de Views de Produtos';

    protected static string|\UnitEnum|null $navigationGroup = 'Relatórios';

    protected static ?int $navigationSort = 10;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('visit_date')
                    ->label('Data')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => \Carbon\Carbon::parse($state)->format('d/m/Y')),

                TextColumn::make('product_name')
                    ->label('Produto')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('product_name', $direction);
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('products.name', 'like', "%{$search}%");
                    }),

                TextColumn::make('views_count')
                    ->label('Total de Views')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('views_count', $direction);
                    })
                    ->badge()
                    ->color('primary'),
            ])
            ->filters([
                Filter::make('product_name')
                    ->label('Nome do produto')
                    ->form([
                        TextInput::make('product_name')
                            ->label('Nome do produto')
                            ->placeholder('Buscar produto...'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            filled($data['product_name']),
                            fn (Builder $q) => $q->where('products.name', 'like', "%{$data['product_name']}%")
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return filled($data['product_name'])
                            ? 'Produto: ' . $data['product_name']
                            : null;
                    }),
            ])
            ->defaultSort('visit_date', 'desc');
    }

    protected function getTableQuery(): Builder
    {
        return UserBrowsingHistory::query()
            ->selectRaw('DATE(user_browsing_history.visited_at) as visit_date, user_browsing_history.product_id, products.name as product_name, COUNT(*) as views_count')
            ->join('products', 'products.id', '=', 'user_browsing_history.product_id')
            ->whereNotNull('user_browsing_history.product_id')
            ->where('user_browsing_history.page_type', 'product')
            ->groupBy(DB::raw('DATE(user_browsing_history.visited_at)'), 'user_browsing_history.product_id', 'products.name')
            ->orderBy('visit_date', 'desc');
    }
}

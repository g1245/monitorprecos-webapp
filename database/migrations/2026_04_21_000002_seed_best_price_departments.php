<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('departments')->updateOrInsert(
            ['id' => 990],
            [
                'name'         => 'Melhor Preço Últimos 7 dias',
                'parent_id'    => null,
                'show_in_menu' => false,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]
        );

        DB::table('departments')->updateOrInsert(
            ['id' => 991],
            [
                'name'         => 'Melhor Preço Últimos 15 dias',
                'parent_id'    => null,
                'show_in_menu' => false,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('departments')->whereIn('id', [990, 991])->delete();
    }
};

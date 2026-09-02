<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedInteger('game_order')
                ->nullable()
                ->after('position');

            $table->timestamp('opened_at')
                ->nullable()
                ->after('game_order');

            $table->timestamp('closed_at')
                ->nullable()
                ->after('opened_at');

            $table->unique([
                'room_id',
                'game_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropUnique([
                'room_id',
                'game_order',
            ]);

            $table->dropColumn([
                'game_order',
                'opened_at',
                'closed_at',
            ]);
        });
    }
};

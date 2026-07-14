<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sale_stock_exit', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sale_id')->nullable(FALSE);
            $table->foreign('sale_id')->references('id')->on('sales')->cascadeOnDelete();

            $table->unsignedBigInteger('stock_exit_id')->nullable(FALSE);
            $table->foreign('stock_exit_id')->references('id')->on('stock_exits')->cascadeOnDelete();

            $table->unique(['sale_id', 'stock_exit_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_stock_exit', function (Blueprint $table) {
            $table->dropForeign(['sale_id']);
            $table->dropForeign(['stock_exit_id']);
        });
        Schema::dropIfExists('sale_stock_exit');
    }
};

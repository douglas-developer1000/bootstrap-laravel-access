<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @see App\Models\StockEntry::class
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_entries', function (Blueprint $table) {
            $table->id();
            $table->decimal('cost', 10, 4)->nullable(FALSE);
            $table->unsignedMediumInteger('qty')->nullable(FALSE);
            $table->dateTime('validity')->nullable();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->unsignedBigInteger('discount_id')->nullable(TRUE);
            $table->foreign('discount_id')
                ->references('id')->on('discounts')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
            $table->dropConstrainedForeignId('supplier_id');
            $table->dropConstrainedForeignId('user_id');
            $table->dropForeign(['discount_id']);
        });
        Schema::dropIfExists('stock_entries');
    }
};

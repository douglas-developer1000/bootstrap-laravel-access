<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @see App\Models\Product::class
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(FALSE);
            $table->text('obs')->nullable();
            $table->string(
                'img',
                \intval(
                    config('database.schema.sizes.generic.img')
                )
            )->nullable();
            $table->jsonb('details')->nullable();
            $table->foreignId('product_category_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_category_id');
            $table->dropConstrainedForeignId('user_id');
        });
        Schema::dropIfExists('products');
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @see App\Models\Supplier::class
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(FALSE);
            $table->string(
                'cnpj',
                \intval(
                    config('database.schema.sizes.generic.cnpj')
                )
            )->nullable();
            $table->string(
                'img',
                \intval(
                    config('database.schema.sizes.generic.img')
                )
            )->nullable();
            $table->string(
                'color',
                \intval(
                    config('database.schema.sizes.generic.color')
                )
            )->nullable();
            $table->text('obs')->nullable();
            $table->boolean('native')->nullable(FALSE)->default(0);
            $table->softDeletes();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
        Schema::dropIfExists('suppliers');
    }
};

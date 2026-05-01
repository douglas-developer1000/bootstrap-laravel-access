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
        Schema::create('register_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email', \intval(
                config('database.schema.sizes.register-order.email')
            ))->unique();
            $table->string('phone', \intval(
                config('database.schema.sizes.register-order.phone')
            ))->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('register_orders');
    }
};

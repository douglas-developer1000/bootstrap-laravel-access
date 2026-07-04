<?php

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
        Schema::dropIfExists('register_orders');
        Schema::dropIfExists('register_approvals');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('register_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email', \intval(
                config('database.schema.sizes.register-order.email')
            ))->unique();
            $table->string('phone', \intval(
                config('database.schema.sizes.generic.phone.max')
            ))->nullable();
            $table->timestamps();
        });
        Schema::create('register_approvals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email', \intval(
                config('database.schema.sizes.register-approval.email')
            ))->unique();
            $table->string('phone', \intval(
                config('database.schema.sizes.generic.phone.max')
            ))->nullable();
            $table->string('token', \intval(
                config('database.schema.sizes.register-approval.token')
            ))->nullable(FALSE);
            $table->timestamp("expiration_data")->nullable(FALSE);
            $table->timestamps();
        });
    }
};

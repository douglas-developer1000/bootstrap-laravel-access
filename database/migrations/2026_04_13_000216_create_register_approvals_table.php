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
        Schema::create('register_approvals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email', \intval(
                config('database.schema.sizes.register-approval.email')
            ))->unique();
            $table->string('phone', \intval(
                config('database.schema.sizes.register-approval.phone')
            ))->nullable();
            $table->string('token', \intval(
                config('database.schema.sizes.register-approval.token')
            ))->nullable(FALSE);
            $table->timestamp("expiration_data")->nullable(FALSE);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('register_approvals');
    }
};

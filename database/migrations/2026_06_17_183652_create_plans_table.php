<?php

declare(strict_types=1);

use App\Libraries\Enums\BillingPeriodEnum;
use App\Models\Plan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @see Plan::class
 */
return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2)->default(0.00);
            $table->enum(
                'billing_period',
                array_column(BillingPeriodEnum::cases(), 'value')
            );
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('plan_role', function (Blueprint $table) {
            $table->id();
            $table->boolean('additional')->nullable()->default(0);
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_role', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plan_id');
            $table->dropConstrainedForeignId('role_id');
        });
        Schema::dropIfExists('plan_role');
        Schema::dropIfExists('plans');
    }
};

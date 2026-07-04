<?php

declare(strict_types=1);

use App\Libraries\Enums\LicenseStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @see License::class
 */
return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * NOTE: the fields starts_at and expires_at must be nullable because
     * when they are both nullable, the license status is "pending" and indicates
     * the app is waiting for payment's finalization
     */
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->numericMorphs('licensable');
            $table->index('licensable_type');

            $table->decimal('price_paid', 8, 2);
            $table->boolean('is_recurring')->nullable(false);

            $table->enum(
                'status',
                array_column(LicenseStatusEnum::cases(), 'value')
            );

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
        Schema::create('license_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('license_role', function (Blueprint $table) {
            $table->dropConstrainedForeignId('license_id');
            $table->dropConstrainedForeignId('role_id');
        });
        Schema::dropIfExists('license_role');
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plan_id');
        });
        Schema::dropIfExists('licenses');
    }
};

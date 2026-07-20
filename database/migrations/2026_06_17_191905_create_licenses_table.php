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
     * NOTE 1: the fields starts_at and expires_at must be nullable because
     * when they are both nullable, the license status is "pending" and indicates
     * the app is waiting for payment's finalization
     *
     * NOTE 2: the licensable_type size must not be greater than 245, because of
     * each character has 4 bytes and the index in VARCHAR(255) would have 1020
     * bytes (and the max is 1000 bytes).
     *
     * NOTE 3: the licensable_type and licensable_id declaration below is
     * equivalent to "$table->numericMorphs('licensable')".
     */
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('plan_id')->constrained()->onDelete('cascade');

            $table->string('licensable_type', 245);
            $table->unsignedBigInteger('licensable_id');
            $table->index(['licensable_type', 'licensable_id'], null);

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

<?php

declare(strict_types=1);

use App\Libraries\Enums\LicenseStatusEnum;
use App\Models\License;
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

            $table->jsonb('additionals')->nullable();
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
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plan_id');
        });
        Schema::dropIfExists('licenses');
    }
};

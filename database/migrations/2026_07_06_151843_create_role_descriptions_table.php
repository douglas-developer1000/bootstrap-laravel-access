<?php

declare(strict_types=1);

use App\Models\RoleDescription;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @see RoleDescription::class
 */
return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableNames = $this->getTableNames();

        Schema::create('role_descriptions', function (Blueprint $table) use ($tableNames) {
            $table->id();

            $table->text('description');

            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')
                ->references('id')->on($tableNames['roles'])->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_descriptions', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
        });
        Schema::dropIfExists('role_descriptions');
    }

    protected function getTableNames(): array
    {
        $tableNames = config('permission.table_names');
        if (empty($tableNames)) {
            throw new RuntimeException(
                'Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.'
            );
        }

        return $tableNames;
    }
};

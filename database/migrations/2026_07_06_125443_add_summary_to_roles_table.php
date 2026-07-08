<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableNames = $this->getTableNames();

        Schema::table($tableNames['roles'], function (Blueprint $table) use ($tableNames) {
            $table->string('summary')->nullable(FALSE);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = $this->getTableNames();

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->dropColumn('summary');
        });
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

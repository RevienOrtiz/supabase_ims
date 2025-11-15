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
        if (!Schema::hasColumn('seniors', 'certification')) {
            Schema::table('seniors', function (Blueprint $table) {
                $table->boolean('certification')->nullable()->after('disability_specify');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('seniors', 'certification')) {
            Schema::table('seniors', function (Blueprint $table) {
                $table->dropColumn('certification');
            });
        }
    }
};

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
        Schema::table('pension_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('pension_applications', 'permanent_income')) {
                $table->string('permanent_income', 10)->nullable();
            }
            if (!Schema::hasColumn('pension_applications', 'income_amount')) {
                $table->string('income_amount', 50)->nullable();
            }
            if (!Schema::hasColumn('pension_applications', 'income_source')) {
                $table->string('income_source', 255)->nullable();
            }
            if (!Schema::hasColumn('pension_applications', 'existing_illness')) {
                $table->string('existing_illness', 10)->nullable();
            }
            if (!Schema::hasColumn('pension_applications', 'illness_specify')) {
                $table->string('illness_specify', 255)->nullable();
            }
            if (!Schema::hasColumn('pension_applications', 'with_disability')) {
                $table->string('with_disability', 10)->nullable();
            }
            if (!Schema::hasColumn('pension_applications', 'disability_specify')) {
                $table->string('disability_specify', 255)->nullable();
            }
            if (!Schema::hasColumn('pension_applications', 'living_arrangement')) {
                $table->json('living_arrangement')->nullable();
            }
            if (!Schema::hasColumn('pension_applications', 'certification')) {
                $table->boolean('certification')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pension_applications', function (Blueprint $table) {
            $drops = [];
            foreach ([
                'permanent_income', 'income_amount', 'income_source',
                'existing_illness', 'illness_specify', 'with_disability',
                'disability_specify', 'living_arrangement', 'certification'
            ] as $col) {
                if (Schema::hasColumn('pension_applications', $col)) {
                    $drops[] = $col;
                }
            }
            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing pension applications to match senior's monthly income
        // DB::statement('UPDATE pension_applications p 
        //               JOIN seniors s ON p.senior_id = s.id 
        //               SET p.monthly_income = s.monthly_income 
        //               WHERE s.monthly_income IS NOT NULL');
        if (Schema::hasColumn('seniors', 'monthly_income')) {
            DB::statement("UPDATE pension_applications p SET monthly_income = s.monthly_income FROM applications a, seniors s WHERE p.application_id = a.id AND a.senior_id = s.id AND s.monthly_income IS NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed for data update
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMonthlyIncomeSyncTrigger extends Migration
{
    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        // Create a trigger to update pension_applications when seniors.monthly_income is updated
        // DB::unprepared('
        //     CREATE TRIGGER sync_monthly_income_after_senior_update
        //     AFTER UPDATE ON seniors
        //     FOR EACH ROW
        //     BEGIN
        //         IF NEW.monthly_income != OLD.monthly_income THEN
        //             UPDATE pension_applications 
        //             SET monthly_income = NEW.monthly_income
        //             WHERE senior_id = NEW.id;
        //         END IF;
        //     END
        // ');
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        // Drop the trigger
        // DB::unprepared('DROP TRIGGER IF EXISTS sync_monthly_income_after_senior_update');
    }
}
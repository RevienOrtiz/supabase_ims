<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateReverseMonthlyIncomeSyncTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create a trigger to update seniors.monthly_income when pension_applications.monthly_income is updated
        // DB::unprepared('
        //     CREATE TRIGGER sync_monthly_income_after_pension_update
        //     AFTER UPDATE ON pension_applications
        //     FOR EACH ROW
        //     BEGIN
        //         IF OLD.monthly_income <> NEW.monthly_income THEN
        //             UPDATE seniors
        //             SET monthly_income = NEW.monthly_income
        //             WHERE id = NEW.senior_id;
        //         END IF;
        //     END
        // ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the trigger
        // DB::unprepared('DROP TRIGGER IF EXISTS sync_monthly_income_after_pension_update');
    }
}
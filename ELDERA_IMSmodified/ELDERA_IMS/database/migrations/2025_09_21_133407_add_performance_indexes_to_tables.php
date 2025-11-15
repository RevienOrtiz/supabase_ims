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
        // Add indexes to seniors table for common queries (check if they don't exist)
        Schema::table('seniors', function (Blueprint $table) {
            if (!$this->indexExists('seniors', 'seniors_status_barangay_index')) {
                $table->index(['status', 'barangay'], 'seniors_status_barangay_index');
            }
            if (!$this->indexExists('seniors', 'seniors_sex_status_index')) {
                $table->index(['sex', 'status'], 'seniors_sex_status_index');
            }
            if (!$this->indexExists('seniors', 'seniors_pension_status_index')) {
                $table->index(['has_pension', 'status'], 'seniors_pension_status_index');
            }
            if (!$this->indexExists('seniors', 'seniors_barangay_status_sex_index')) {
                $table->index(['barangay', 'status', 'sex'], 'seniors_barangay_status_sex_index');
            }
            if (!$this->indexExists('seniors', 'seniors_date_of_birth_index')) {
                $table->index(['date_of_birth'], 'seniors_date_of_birth_index');
            }
        });

        // Add indexes to applications table for common queries
        Schema::table('applications', function (Blueprint $table) {
            if (!$this->indexExists('applications', 'applications_type_status_index')) {
                $table->index(['application_type', 'status'], 'applications_type_status_index');
            }
            if (!$this->indexExists('applications', 'applications_senior_type_index')) {
                $table->index(['senior_id', 'application_type'], 'applications_senior_type_index');
            }
            if (!$this->indexExists('applications', 'applications_status_created_index')) {
                $table->index(['status', 'created_at'], 'applications_status_created_index');
            }
            if (!$this->indexExists('applications', 'applications_submitted_at_index')) {
                $table->index(['submitted_at'], 'applications_submitted_at_index');
            }
        });

        // Add indexes to events table for common queries
        Schema::table('events', function (Blueprint $table) {
            if (!$this->indexExists('events', 'events_date_time_index')) {
                $table->index(['event_date', 'start_time'], 'events_date_time_index');
            }
            if (!$this->indexExists('events', 'events_type_date_index')) {
                $table->index(['event_type', 'event_date'], 'events_type_date_index');
            }
        });

        // Add indexes to barangays table
        Schema::table('barangays', function (Blueprint $table) {
            if (!$this->indexExists('barangays', 'barangays_active_name_index')) {
                $table->index(['is_active', 'name'], 'barangays_active_name_index');
            }
        });
    }

    private function indexExists($table, $indexName)
    {
        $table = strtolower($table);
        $rows = DB::select(
            "SELECT 1 FROM pg_indexes WHERE schemaname = current_schema() AND tablename = ? AND indexname = ?",
            [$table, $indexName]
        );
        return count($rows) > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seniors', function (Blueprint $table) {
            $table->dropIndex('seniors_status_barangay_index');
            $table->dropIndex('seniors_sex_status_index');
            $table->dropIndex('seniors_pension_status_index');
            $table->dropIndex('seniors_barangay_status_sex_index');
            $table->dropIndex('seniors_created_at_index');
            $table->dropIndex('seniors_date_of_birth_index');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex('applications_type_status_index');
            $table->dropIndex('applications_senior_type_index');
            $table->dropIndex('applications_status_created_index');
            $table->dropIndex('applications_submitted_at_index');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('events_date_time_index');
            $table->dropIndex('events_type_date_index');
        });

        Schema::table('barangays', function (Blueprint $table) {
            $table->dropIndex('barangays_active_name_index');
        });
    }
};
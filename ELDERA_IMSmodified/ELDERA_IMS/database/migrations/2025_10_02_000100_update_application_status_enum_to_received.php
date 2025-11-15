<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Expand ENUM to include 'received' so updates won't truncate
        // DB::statement("ALTER TABLE applications MODIFY status ENUM('pending','under_review','approved','rejected','completed','received') NOT NULL DEFAULT 'pending'");

        // Step 2: Update existing data: map under_review and completed to received
        DB::table('applications')
            ->whereIn('status', ['under_review', 'completed'])
            ->update(['status' => 'received']);

        // Step 3: Shrink ENUM to remove deprecated values
        // DB::statement("ALTER TABLE applications MODIFY status ENUM('pending','received','approved','rejected') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Step 1: Map received back to under_review
        DB::table('applications')
            ->where('status', 'received')
            ->update(['status' => 'under_review']);

        // Step 2: Reintroduce previous enum options and remove 'received'
        // DB::statement("ALTER TABLE applications MODIFY status ENUM('pending','under_review','approved','rejected','completed') NOT NULL DEFAULT 'pending'");
    }
};
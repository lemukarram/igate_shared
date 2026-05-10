<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Settings are stored in the 'settings' table by Spatie Laravel Settings
        // We need to add the properties to the 'general' group.
        // Usually, this is done via a migration that inserts rows into the settings table.
        
        DB::table('settings')->insert([
            ['group' => 'general', 'name' => 'contact_email', 'payload' => json_encode('info@igate.com')],
            ['group' => 'general', 'name' => 'contact_phone', 'payload' => json_encode('+966000000000')],
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('group', 'general')->whereIn('name', ['contact_email', 'contact_phone'])->delete();
    }
};

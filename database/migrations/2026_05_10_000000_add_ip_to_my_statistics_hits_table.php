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
        Schema::table('my_statistics_hits', function (Blueprint $table) {
            $table->string('ip', 45)->nullable()->after('visitor_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('my_statistics_hits', function (Blueprint $table) {
            $table->dropColumn('ip');
        });
    }
};

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
        if (!Schema::hasTable('my_statistics_hits')) {
            Schema::create('my_statistics_hits', function (Blueprint $table) {
                $table->id();
                $table->string('visitor_hash', 64)->index();
                $table->text('url')->nullable();
                $table->string('title')->nullable();
                $table->text('referrer')->nullable();
                $table->string('search_engine')->nullable();
                $table->string('browser')->nullable();
                $table->string('os')->nullable();
                $table->string('device')->nullable();
                $table->string('country', 2)->nullable()->index();
                $table->string('utm_source')->nullable();
                $table->string('utm_medium')->nullable();
                $table->string('utm_campaign')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_statistics_hits');
    }
};

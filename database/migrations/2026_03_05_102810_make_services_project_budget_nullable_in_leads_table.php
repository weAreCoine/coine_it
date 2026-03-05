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
        Schema::table('leads', function (Blueprint $table) {
            $table->json('services')->nullable()->change();
            $table->longText('project')->nullable()->change();
            $table->string('budget')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->json('services')->nullable(false)->change();
            $table->longText('project')->nullable(false)->change();
            $table->string('budget')->nullable(false)->change();
        });
    }
};

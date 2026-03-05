<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->string('slug')->after('name')->default('');
        });

        DB::table('tags')->eachById(function ($tag) {
            DB::table('tags')
                ->where('id', $tag->id)
                ->update(['slug' => Str::slug($tag->name)]);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->string('slug')->unique()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};

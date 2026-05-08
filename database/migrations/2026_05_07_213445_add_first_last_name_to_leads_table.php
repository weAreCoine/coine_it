<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('uuid');
            $table->string('last_name')->nullable()->after('first_name');
        });

        DB::table('leads')
            ->whereNotNull('name')
            ->orderBy('id')
            ->each(function (object $lead): void {
                $name = trim((string) $lead->name);

                if ($name === '') {
                    return;
                }

                $parts = preg_split('/\s+/u', $name, 2) ?: [];
                $firstName = $parts[0] ?? null;
                $lastName = $parts[1] ?? null;

                DB::table('leads')->where('id', $lead->id)->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};

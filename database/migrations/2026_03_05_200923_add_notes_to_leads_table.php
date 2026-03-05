<?php

use App\Enums\LeadStage;
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
            $table->text('notes')->nullable();
            $table->enum('stage', array_column(LeadStage::cases(), 'value'))
                ->default(LeadStage::NEW->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('notes');
            $table->dropColumn('stage');
        });
    }
};

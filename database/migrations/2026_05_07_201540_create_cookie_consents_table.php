<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cookie_consents', function (Blueprint $table) {
            $table->id();
            $table->uuid('consent_id')->unique();
            $table->uuid('external_id')->nullable()->index();
            $table->boolean('necessary')->default(true);
            $table->boolean('marketing');
            $table->boolean('analytics');
            $table->string('choice_type', 20)->index();
            $table->char('ip_hash', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referer', 2048)->nullable();
            $table->string('path', 2048);
            $table->string('version', 20);
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cookie_consents');
    }
};

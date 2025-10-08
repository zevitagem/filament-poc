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
        Schema::create('campaign_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->string('application_id', 100);
            $table->string('list_id', 100);
            $table->string('segment_id', 100);
            $table->timestamps();
            
            // Unique constraint to prevent duplicates
            $table->unique(['campaign_id', 'application_id', 'list_id', 'segment_id'], 'unique_campaign_recipient');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_recipients');
    }
};

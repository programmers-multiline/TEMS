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
        Schema::create('ps_transfer_request_items', function (Blueprint $table) {
            $table->id();
            $table->integer('transfer_request_id');
            $table->integer('tool_id');
            $table->integer('request_number');
            $table->integer('user_id');
            $table->integer('status')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ps_transfer_request_items');
    }
};

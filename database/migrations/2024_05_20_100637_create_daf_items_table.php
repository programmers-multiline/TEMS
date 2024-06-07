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
        Schema::create('daf_items', function (Blueprint $table) { 
            $table->id();
            $table->integer('daf_id');
            $table->integer('tool_id');
            $table->integer('daf_number');
            $table->integer('user_id');
            $table->integer('item_status')->default('0');
            $table->integer('status')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daf_items');
    }
};

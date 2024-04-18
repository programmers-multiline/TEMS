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
        Schema::create('tools_and_equipment', function (Blueprint $table) {
            $table->id();
            $table->integer('po_number');
            $table->string('asset_code');
            $table->string('serial_number');
            $table->string('item_code');
            $table->string('item_description');
            $table->string('brand');
            $table->string('location');
            $table->string('tools_status');
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools_and_equipment');
    }
};

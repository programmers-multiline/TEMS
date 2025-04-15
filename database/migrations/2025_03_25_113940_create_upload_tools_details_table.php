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
        Schema::create('upload_tools_details', function (Blueprint $table) {
            $table->id();
            $table->integer('tools_upload_id');
            $table->string('item_code');
            $table->string('item_description');
            $table->integer('qty');
            $table->string('teis_ref', 50);
            $table->string('asset_code', 30);
            $table->decimal('cost', 8, 2)->nullable();
            $table->integer('approver_status')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_tools_details');
    }
};

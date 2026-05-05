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
        Schema::create('camera_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camera_model_id')->constrained()->onDelete('cascade');
            $table->string('package_name');
            $table->text('includes')->nullable();
            $table->decimal('daily_price', 10, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camera_packages');
    }
};

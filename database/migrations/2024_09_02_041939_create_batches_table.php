<?php

use App\Models\Hmo;
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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hmo_id')
                ->references('id')
                ->on('hmos')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('provider_name');

            $table->timestamps();

            $table->unique(['hmo_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};

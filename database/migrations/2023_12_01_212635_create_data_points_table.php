<?php

use App\Models\Tracker;
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
        Schema::create('data_points', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Tracker::class)->constrained()->onDelete('cascade');
            $table->date('date');
            $table->float('value')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_points');
    }
};

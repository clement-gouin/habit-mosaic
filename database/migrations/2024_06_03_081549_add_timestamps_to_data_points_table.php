<?php

use App\Models\DataPoint;
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
        Schema::table('data_points', function (Blueprint $table) {
            $table->timestamps();
        });

        DataPoint::all()->each(function (DataPoint $dataPoint) {
            $dataPoint->update([
                'created_at' => $dataPoint->date,
                'updated_at' => $dataPoint->date,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_points', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};

<?php

use Carbon\Carbon;
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
        DataPoint::query()
            ->where('date', '=', Carbon::createFromTimestamp(0))
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

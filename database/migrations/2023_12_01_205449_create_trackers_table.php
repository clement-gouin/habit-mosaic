<?php

use App\Models\User;
use App\Models\Category;
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
        Schema::create('trackers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Category::class)->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->integer('order');
            $table->string('icon');
            $table->string('unit')->nullable();
            $table->float('value_step');
            $table->float('target_value');
            $table->float('target_score');
            $table->boolean('single');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trackers');
    }
};

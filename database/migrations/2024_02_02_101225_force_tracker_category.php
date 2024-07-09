<?php

use App\Models\Category;
use App\Models\User;
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
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('trackers', function (Blueprint $table) {
                $table->dropForeign('trackers_category_id_foreign');
                $table->foreignIdFor(Category::class)->nullable(false)->change()->constrained()->cascadeOnDelete();
                $table->dropConstrainedForeignIdFor(User::class);
            });
        } else {
            Schema::dropIfExists('trackers');

            Schema::create('trackers', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(Category::class)->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->integer('order');
                $table->string('icon');
                $table->string('unit')->nullable();
                $table->float('value_step');
                $table->float('target_value');
                $table->float('target_score');
                $table->boolean('single');
                $table->timestamps();
                $table->boolean('overflow')->default(true);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('trackers', function (Blueprint $table) {
                $table->dropForeign('trackers_category_id_foreign');
                $table->foreignIdFor(Category::class)->nullable(false)->change()->constrained()->cascadeOnDelete();
                $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnDelete();
            });
        } else {
            Schema::dropIfExists('trackers');

            Schema::create('trackers', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnDelete();
                $table->foreignIdFor(Category::class)->nullable()->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->integer('order');
                $table->string('icon');
                $table->string('unit')->nullable();
                $table->float('value_step');
                $table->float('target_value');
                $table->float('target_score');
                $table->boolean('single');
                $table->timestamps();
                $table->boolean('overflow')->default(true);
            });
        }
    }
};

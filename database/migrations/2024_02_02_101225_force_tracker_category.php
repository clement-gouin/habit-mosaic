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
            Schema::table('trackers', function (Blueprint $table) {
                $table->dropColumn(['category_id', 'user_id']);
            });
            Schema::table('trackers', function (Blueprint $table) {
                $table->foreignIdFor(Category::class)->constrained()->cascadeOnDelete();
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
            Schema::table('trackers', function (Blueprint $table) {
                $table->dropColumn('category_id');
            });
            Schema::table('trackers', function (Blueprint $table) {
                $table->foreignIdFor(Category::class)->nullable()->constrained()->nullOnDelete();
                $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnDelete();
            });
        }
    }
};

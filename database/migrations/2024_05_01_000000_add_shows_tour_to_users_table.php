<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('shows_tour')->default(false)->after('visit_count');
        });
        
        // Set shows_tour to true for all existing users with low visit counts
        DB::table('users')->where('visit_count', '<=', 2)->update(['shows_tour' => true]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('shows_tour');
        });
    }
}; 
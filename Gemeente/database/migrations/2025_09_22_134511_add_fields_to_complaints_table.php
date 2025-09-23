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
        Schema::table('complaints', function (Blueprint $table) {
            $table->string('category')->nullable()->after('description');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('category');
            $table->string('location')->nullable()->after('lng');
            $table->string('reporter_phone')->nullable()->after('reporter_email');
            $table->text('internal_notes')->nullable()->after('reporter_phone');
            $table->timestamp('resolved_at')->nullable()->after('internal_notes');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->after('resolved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn([
                'category',
                'priority', 
                'location',
                'reporter_phone',
                'internal_notes',
                'resolved_at',
                'assigned_to'
            ]);
        });
    }
};

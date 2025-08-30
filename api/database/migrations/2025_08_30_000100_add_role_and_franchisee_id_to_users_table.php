<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'franchisee'])->default('franchisee')->after('password');
            $table->foreignId('franchisee_id')->nullable()->after('role')->constrained('franchisees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['franchisee_id']);
            $table->dropColumn(['role', 'franchisee_id']);
        });
    }
};



<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            if (!Schema::hasColumn('clients', 'remember_token')) {
                $table->rememberToken()->nullable()->after('password');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
            if (Schema::hasColumn('clients', 'password')) {
                $table->dropColumn('password');
            }
        });
    }
};

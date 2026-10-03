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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('avatar_url')->nullable()->after('phone');
            $table->string('status', 30)->default('active')->index()->after('avatar_url');
            $table->text('bio')->nullable()->after('status');
            $table->json('metadata')->nullable()->after('bio');
            $table->timestamp('last_login_at')->nullable()->after('updated_at');
            $table->softDeletes()->after('last_login_at');
            $table->index(['tenant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropIndex(['tenant_id', 'status']);
            $table->dropColumn([
                'tenant_id',
                'first_name',
                'last_name',
                'phone',
                'avatar_url',
                'status',
                'bio',
                'metadata',
                'last_login_at',
                'deleted_at',
            ]);
        });
    }
};

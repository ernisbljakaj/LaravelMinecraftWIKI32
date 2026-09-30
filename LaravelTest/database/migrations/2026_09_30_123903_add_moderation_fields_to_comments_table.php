<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->string('moderation_status')->default('approved')->after('approved');
            $table->string('moderation_source')->nullable()->after('moderation_status');
            $table->text('moderation_reason')->nullable()->after('moderation_source');
            $table->decimal('moderation_confidence', 4, 3)->nullable()->after('moderation_reason');
            $table->timestamp('moderated_at')->nullable()->after('moderation_confidence');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn([
                'moderation_status',
                'moderation_source',
                'moderation_reason',
                'moderation_confidence',
                'moderated_at',
            ]);
        });
    }
};

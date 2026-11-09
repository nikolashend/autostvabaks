<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            // Admin-created pages are published (is_active) and indexable separately,
            // so a draft or thin page can exist without entering the sitemap.
            $table->boolean('is_indexable')->default(true)->after('is_active');
        });

        Schema::table('leads', function (Blueprint $table) {
            // Optional campaign attribution captured from the landing URL; never required.
            $table->string('utm_source', 200)->nullable()->after('referrer');
            $table->string('utm_medium', 200)->nullable()->after('utm_source');
            $table->string('utm_campaign', 200)->nullable()->after('utm_medium');
            $table->string('utm_content', 200)->nullable()->after('utm_campaign');
            $table->string('utm_term', 200)->nullable()->after('utm_content');
            $table->string('gclid', 200)->nullable()->after('utm_term');
        });
    }

    public function down(): void
    {
        Schema::table('pages', fn (Blueprint $table) => $table->dropColumn('is_indexable'));
        Schema::table('leads', fn (Blueprint $table) => $table->dropColumn([
            'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'gclid',
        ]));
    }
};

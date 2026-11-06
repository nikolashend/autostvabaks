<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('reg_number', 20)->index();
            $table->string('phone', 32);
            $table->string('email');
            $table->unsignedInteger('desired_price')->nullable();
            $table->string('language', 5)->default('et');   // preferred language of communication
            $table->unsignedInteger('mileage')->nullable();
            $table->text('info')->nullable();
            $table->json('photos')->nullable();             // paths on the private "local" disk
            $table->string('locale', 5)->default('et');     // site language the lead was sent from
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->string('referrer', 512)->nullable();
            $table->timestamp('mail_sent_at')->nullable();
            $table->string('mail_error', 512)->nullable();
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('author');
            $table->json('location')->nullable();   // {et, ru, en}
            $table->json('title')->nullable();      // {et, ru, en}
            $table->json('text');                   // {et, ru, en}
            $table->unsignedTinyInteger('rating')->default(5);
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->json('question');   // {et, ru, en}
            $table->json('answer');     // {et, ru, en}
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('key')->nullable()->unique();    // stable handle for system pages: terms, privacy
            $table->string('slug_et')->unique();
            $table->string('slug_ru')->unique();
            $table->string('slug_en')->unique();
            $table->json('title');              // {et, ru, en}
            $table->json('content')->nullable();            // {et, ru, en} – HTML
            $table->json('meta_description')->nullable();   // {et, ru, en}
            $table->string('footer_group', 20)->default('info'); // info | legal | none
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('leads');
    }
};

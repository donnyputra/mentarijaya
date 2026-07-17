<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBbbbBackendTables extends Migration
{
    public function up()
    {
        Schema::create('bbbb_users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 191)->unique();
            $table->string('password');
            $table->string('full_name', 120);
            $table->date('date_of_birth')->nullable();
            $table->string('timezone', 64)->default('Asia/Jakarta');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('bbbb_bible_versions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('name', 120);
            $table->string('language', 32)->default('id');
            $table->text('description');
            $table->string('size_label', 64);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('bbbb_bible_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('version_id')->constrained('bbbb_bible_versions')->onDelete('cascade');
            $table->string('external_id', 64);
            $table->string('name', 120);
            $table->integer('sort_order');
            $table->timestamps();
            $table->index(['version_id', 'sort_order']);
            $table->unique(['version_id', 'external_id']);
        });

        Schema::create('bbbb_bible_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('bbbb_bible_books')->onDelete('cascade');
            $table->string('external_id', 64);
            $table->string('book_external_id', 64);
            $table->string('book_name', 120);
            $table->integer('chapter_number');
            $table->text('summary');
            $table->json('verses');
            $table->timestamps();
            $table->index('external_id');
            $table->index(['book_id', 'chapter_number']);
            $table->unique(['book_id', 'chapter_number']);
        });

        Schema::create('bbbb_bible_verses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained('bbbb_bible_chapters')->onDelete('cascade');
            $table->integer('verse_number');
            $table->text('text');
            $table->timestamps();
            $table->unique(['chapter_id', 'verse_number']);
        });

        Schema::create('bbbb_reading_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('bbbb_users')->onDelete('cascade');
            $table->string('version_code', 64);
            $table->foreignId('bible_chapter_id')->constrained('bbbb_bible_chapters')->onDelete('cascade');
            $table->date('read_on');
            $table->timestamps();
            $table->unique(['user_id', 'version_code', 'bible_chapter_id', 'read_on'], 'bbbb_reading_unique');
        });

        Schema::create('bbbb_daily_bread_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('bbbb_users')->onDelete('cascade');
            $table->date('share_date')->index();
            $table->string('version_code', 64);
            $table->text('reflection_text');
            $table->text('takeaway_text');
            $table->string('status', 32)->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('bbbb_daily_bread_passages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_bread_post_id')->constrained('bbbb_daily_bread_posts')->onDelete('cascade');
            $table->foreignId('bible_chapter_id')->constrained('bbbb_bible_chapters')->onDelete('cascade');
            $table->string('chapter_external_id', 64);
            $table->string('version_code', 64);
            $table->string('reference_label', 120);
            $table->string('book_name', 120);
            $table->integer('chapter_number');
            $table->integer('start_verse')->nullable();
            $table->integer('end_verse')->nullable();
            $table->integer('sort_order');
            $table->timestamps();
            $table->index('daily_bread_post_id');
        });

        Schema::create('bbbb_daily_bread_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_bread_post_id')->constrained('bbbb_daily_bread_posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('bbbb_users')->onDelete('cascade');
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('bbbb_daily_bread_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_bread_post_id')->constrained('bbbb_daily_bread_posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('bbbb_users')->onDelete('cascade');
            $table->string('kind', 32);
            $table->timestamps();
            $table->unique(['daily_bread_post_id', 'user_id', 'kind'], 'bbbb_reaction_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bbbb_daily_bread_reactions');
        Schema::dropIfExists('bbbb_daily_bread_comments');
        Schema::dropIfExists('bbbb_daily_bread_passages');
        Schema::dropIfExists('bbbb_daily_bread_posts');
        Schema::dropIfExists('bbbb_reading_logs');
        Schema::dropIfExists('bbbb_bible_verses');
        Schema::dropIfExists('bbbb_bible_chapters');
        Schema::dropIfExists('bbbb_bible_books');
        Schema::dropIfExists('bbbb_bible_versions');
        Schema::dropIfExists('bbbb_users');
    }
}

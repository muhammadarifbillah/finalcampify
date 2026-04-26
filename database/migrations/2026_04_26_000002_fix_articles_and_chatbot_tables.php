<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('articles') && !Schema::hasColumn('articles', 'id')) {
            Schema::rename('articles', 'articles_old');

            Schema::create('articles', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('content');
                $table->string('image')->nullable();
                $table->timestamps();
            });

            $rows = DB::table('articles_old')->select('title', 'content', 'image')->get();
            foreach ($rows as $row) {
                DB::table('articles')->insert([
                    'title' => $row->title,
                    'content' => $row->content,
                    'image' => $row->image,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Schema::dropIfExists('articles_old');
        }

        if (Schema::hasTable('chatbot_responses') && !Schema::hasColumn('chatbot_responses', 'id')) {
            Schema::rename('chatbot_responses', 'chatbot_responses_old');

            Schema::create('chatbot_responses', function (Blueprint $table) {
                $table->id();
                $table->string('keyword');
                $table->text('response');
                $table->timestamps();
            });

            $rows = DB::table('chatbot_responses_old')->select('keyword', 'response')->get();
            foreach ($rows as $row) {
                DB::table('chatbot_responses')->insert([
                    'keyword' => $row->keyword,
                    'response' => $row->response,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Schema::dropIfExists('chatbot_responses_old');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback required for repaired tables.
    }
};

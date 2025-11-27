<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Move existing images to Media Library
        $posts = Post::all();

        foreach ($posts as $post) {
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                $post
                    ->addMedia(Storage::disk('public')->path($post->image))
                    ->toMediaCollection('image', 'public');

                // Optional: delete old file after moving
                Storage::disk('public')->delete($post->image);
            }
        }

        // Step 2: Drop the old image column
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('image')->nullable();
        });
    }
};

<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = $this->faker->sentence();

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'content' => $this->faker->paragraphs(5, true),
            'category_id' => Category::inRandomOrder()->value('id') ?? Category::factory(),
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'published_at' => $this->faker->optional()->dateTime(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Post $post) {

            // Path to store fake images locally
            $imagePath = storage_path('app/public/fake-images');

            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            $fileName = 'post_' . $post->id . '.jpg';
            $fileFullPath = $imagePath . '/' . $fileName;

            // Create a local placeholder image (1200x800)
            $this->faker->image($imagePath, 1200, 800, null, false);

            // Add image to media collection
            $post->addMedia($fileFullPath)
                 ->toMediaCollection('image');
        });
    }
}

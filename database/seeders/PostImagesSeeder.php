<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class PostImagesSeeder extends Seeder
{
    public function run(): void
    {
        $posts = Post::with('category')->get();
        $this->command->info("Adding placeholder images to " . $posts->count() . " posts...");

        $progressBar = $this->command->getOutput()->createProgressBar($posts->count());
        $progressBar->start();

        // Category-based colors (RGB)
        $colors = [
            'Technology' => [59, 130, 246],   // Blue
            'Health' => [16, 185, 129],       // Green
            'Sports' => [239, 68, 68],        // Red
            'Science' => [139, 92, 246],      // Purple
            'Politics' => [245, 158, 11],     // Yellow
            'Entertainment' => [236, 72, 153] // Pink
        ];

        foreach ($posts as $post) {
            try {
                $categoryName = $post->category->name;
                $color = $colors[$categoryName] ?? [59, 130, 246]; // Default blue
                
                // Create image resource
                $image = imagecreatetruecolor(1200, 800);
                
                if ($image === false) {
                    throw new \Exception('Failed to create image resource');
                }

                // Allocate colors
                $bgColor = imagecolorallocate($image, $color[0], $color[1], $color[2]);
                $textColor = imagecolorallocate($image, 255, 255, 255);
                $darkColor = imagecolorallocate($image, 
                    max(0, $color[0] - 40), 
                    max(0, $color[1] - 40), 
                    max(0, $color[2] - 40)
                );

                // Fill background with gradient
                for ($i = 0; $i < 800; $i++) {
                    $gradientColor = imagecolorallocate(
                        $image, 
                        max(0, $color[0] - (int)($i / 20)),
                        max(0, $color[1] - (int)($i / 20)),
                        max(0, $color[2] - (int)($i / 20))
                    );
                    imagefilledrectangle($image, 0, $i, 1200, $i + 1, $gradientColor);
                }

                // Add some decorative elements
                for ($i = 0; $i < 5; $i++) {
                    $circleX = rand(100, 1100);
                    $circleY = rand(100, 700);
                    $circleSize = rand(50, 150);
                    imagefilledellipse($image, $circleX, $circleY, $circleSize, $circleSize, $darkColor);
                }

                // Prepare title text
                $title = $post->title;
                if (strlen($title) > 60) {
                    $title = substr($title, 0, 57) . '...';
                }

                // Add title (using basic text since we don't have font files)
                $fontSize = 5; // Basic GD font size
                $titleX = 600;
                $titleY = 350;
                
                // Center the text manually
                $textWidth = strlen($title) * imagefontwidth($fontSize);
                $textHeight = imagefontheight($fontSize);
                $titleX = (1200 - $textWidth) / 2;
                
                imagestring($image, $fontSize, $titleX, $titleY, $title, $textColor);

                // Add category name
                $categoryText = "Category: " . $categoryName;
                $categoryWidth = strlen($categoryText) * imagefontwidth($fontSize);
                $categoryX = (1200 - $categoryWidth) / 2;
                imagestring($image, $fontSize, $categoryX, $titleY + 40, $categoryText, $textColor);

                // Add author name
                $authorText = "By " . $post->user->name;
                $authorWidth = strlen($authorText) * imagefontwidth($fontSize);
                $authorX = (1200 - $authorWidth) / 2;
                imagestring($image, $fontSize, $authorX, $titleY + 80, $authorText, $textColor);

                // Ensure storage directory exists
                $storagePath = storage_path('app/public/temp');
                if (!file_exists($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }

                // Save image to temporary file
                $tempPath = $storagePath . '/post_' . $post->id . '.png';
                
                if (!imagepng($image, $tempPath)) {
                    throw new \Exception('Failed to save image to: ' . $tempPath);
                }

                // Free memory
                imagedestroy($image);

                // Verify file was created
                if (!file_exists($tempPath)) {
                    throw new \Exception('Temporary file was not created: ' . $tempPath);
                }

                // Clear any existing media
                $post->clearMediaCollection('image');

                // Add to media library
                $post->addMedia($tempPath)
                    ->withCustomProperties([
                        'generated' => true,
                        'category' => $categoryName,
                        'color' => 'rgb(' . $color[0] . ',' . $color[1] . ',' . $color[2] . ')'
                    ])
                    ->toMediaCollection('image');

                // Clean up temp file
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

            } catch (\Exception $e) {
                $this->command->warn("Failed to add image to post {$post->id}: {$e->getMessage()}");
                // Continue with next post
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine(2);
        $this->command->info("✅ Images added successfully!");
    }
}
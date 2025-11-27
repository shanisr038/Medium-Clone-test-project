<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('posts')->truncate();
        DB::table('categories')->truncate();
        DB::table('users')->where('email', '!=', 'admin@example.com')->delete();

        $faker = Faker::create('en_US');

        // 1️⃣ Create test user
        $testUser = User::create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // 2️⃣ Create 10 random users with realistic names
        $users = User::factory(10)->create();
        $allUsers = $users->push($testUser);

        // 3️⃣ Create categories
        $categories = ['Technology', 'Health', 'Sports', 'Science', 'Politics', 'Entertainment'];
        foreach ($categories as $cat) {
            Category::create(['name' => $cat, 'slug' => Str::slug($cat)]);
        }
        $categoryIds = Category::pluck('id')->toArray();

        // 4️⃣ Generate posts with realistic English content - 1000 posts per user
        $postsData = [];

        // Realistic topics for each category
        $categoryTopics = [
            'Technology' => [
                'The Future of Artificial Intelligence in Everyday Life',
                'How Blockchain Technology is Revolutionizing Industries',
                'The Impact of 5G on Mobile Connectivity',
                'Cybersecurity Threats in the Modern Digital Age',
                'The Rise of Quantum Computing and Its Applications',
                'Cloud Computing: Benefits and Challenges for Businesses',
                'The Internet of Things and Smart Home Revolution',
                'Mobile App Development Trends in 2024',
                'Data Privacy Laws and User Protection',
                'The Role of Big Data in Business Decision Making',
                'Virtual Reality and Augmented Reality Applications',
                'The Evolution of Programming Languages',
                'Machine Learning Algorithms and Their Uses',
                'Software Development Methodologies Compared',
                'The Future of Web Development',
                'Database Management Systems Overview',
                'Network Security Best Practices',
                'Cloud Storage Solutions for Businesses',
                'The Impact of Social Media on Technology',
                'Open Source Software Benefits and Challenges'
            ],
            'Health' => [
                'Benefits of Mediterranean Diet for Longevity',
                'Mental Health Awareness in the Workplace',
                'The Importance of Regular Exercise for Heart Health',
                'Understanding Sleep Disorders and Their Treatments',
                'Nutrition Tips for Boosting Immune System',
                'Yoga and Meditation for Stress Reduction',
                'Preventive Healthcare and Regular Checkups',
                'Managing Chronic Conditions Through Lifestyle',
                'The Science Behind Healthy Aging',
                'Exercise Routines for Busy Professionals',
                'Healthy Eating Habits for Weight Management',
                'Mental Wellness Strategies for Modern Life',
                'The Role of Hydration in Overall Health',
                'Alternative Medicine and Complementary Therapies',
                'Fitness Tracking Technology and Health Monitoring',
                'Nutritional Supplements and Their Benefits',
                'Public Health Initiatives and Community Wellness',
                'Healthcare Access and Equity Issues',
                'Medical Research Breakthroughs and Innovations',
                'Healthy Lifestyle Changes for Better Living'
            ],
            'Sports' => [
                'The Evolution of Basketball Training Techniques',
                'How Technology is Changing Professional Sports',
                'The Psychology of Winning in Competitive Sports',
                'Women in Sports: Breaking Barriers and Records',
                'The Economic Impact of Major Sporting Events',
                'Nutrition and Hydration for Athletes',
                'Injury Prevention and Recovery Methods',
                'The Business of Sports Management',
                'Youth Sports Development Programs',
                'Olympic Games and Global Unity',
                'Sports Analytics and Performance Metrics',
                'The History of Popular Sports',
                'Extreme Sports and Adventure Activities',
                'Sports Equipment Technology Advances',
                'Coaching Strategies and Team Dynamics',
                'Sports Medicine and Athletic Training',
                'International Sports Competitions and Rivalries',
                'Sports Broadcasting and Media Coverage',
                'Athlete Endorsements and Sponsorships',
                'The Future of Sports Entertainment'
            ],
            'Science' => [
                'Recent Breakthroughs in Climate Change Research',
                'The Search for Life on Other Planets',
                'Advances in Genetic Engineering and CRISPR',
                'Understanding Black Holes and Space-Time',
                'The Role of Microplastics in Environmental Pollution',
                'Renewable Energy Technologies and Innovations',
                'Marine Biology and Ocean Conservation',
                'Neuroscience and Brain Research Developments',
                'Archaeological Discoveries and Human History',
                'Weather Patterns and Climate Modeling',
                'Chemical Reactions and Their Applications',
                'Physics Principles in Everyday Life',
                'Biological Diversity and Ecosystem Health',
                'Scientific Method and Research Processes',
                'Space Exploration Missions and Discoveries',
                'Environmental Science and Sustainability',
                'Medical Science and Disease Research',
                'Materials Science and Engineering',
                'Astronomy and Cosmic Phenomena',
                'Scientific Ethics and Responsible Research'
            ],
            'Politics' => [
                'Global Diplomacy in the 21st Century',
                'The Impact of Social Media on Political Campaigns',
                'Climate Change Policies Around the World',
                'Economic Recovery Strategies Post-Pandemic',
                'The Future of International Trade Agreements',
                'Education Reform and Policy Making',
                'Healthcare Systems and Government Role',
                'Immigration Policies and Global Mobility',
                'Urban Planning and Sustainable Cities',
                'Digital Governance and E-Government Services',
                'Political Systems and Governance Models',
                'International Relations and Foreign Policy',
                'Human Rights and Social Justice Issues',
                'Economic Policies and Market Regulation',
                'Public Administration and Civil Service',
                'Political Philosophy and Ideologies',
                'Election Systems and Democratic Processes',
                'National Security and Defense Policies',
                'Social Welfare Programs and Implementation',
                'Political Communication and Media Influence'
            ],
            'Entertainment' => [
                'The Streaming Wars: Netflix vs Disney+ vs Amazon',
                'The Resurgence of Independent Cinema',
                'How Social Media is Shaping Music Industry',
                'The Evolution of Video Game Storytelling',
                'Virtual Reality Concerts and the Future of Live Events',
                'The Business of Podcasting and Audio Content',
                'Film Industry Adaptations to Digital Platforms',
                'Music Streaming Services and Artist Revenue',
                'The Impact of TikTok on Entertainment',
                'Broadway and Live Theater Innovations',
                'Animation Techniques and Digital Art',
                'Celebrity Culture and Fan Communities',
                'Film Festivals and Independent Productions',
                'Music Production Technology Advances',
                'Gaming Industry Trends and Developments',
                'Television Programming and Streaming Services',
                'Entertainment Law and Copyright Issues',
                'Cultural Impact of Entertainment Media',
                'Live Performance Arts and Theater',
                'Digital Content Creation and Distribution'
            ]
        ];

        $this->command->info("🚀 Starting to generate 1000 posts for each of " . $allUsers->count() . " users...");
        $this->command->info("📊 Total posts to create: " . ($allUsers->count() * 1000));

        $progressBar = $this->command->getOutput()->createProgressBar($allUsers->count() * 1000);
        $progressBar->start();

        foreach ($allUsers as $user) {
            for ($i = 0; $i < 1000; $i++) {
                $category = $faker->randomElement($categories);
                $categoryId = array_search($category, $categories) + 1;
                
                // Use realistic title from our topics or generate a good one
                if ($faker->boolean(70) && !empty($categoryTopics[$category])) {
                    $title = $faker->randomElement($categoryTopics[$category]);
                    // Add some variation to avoid exact duplicates
                    if ($faker->boolean(30)) {
                        $title = $this->modifyTitle($title, $faker);
                    }
                } else {
                    $title = $faker->sentence(6);
                }

                $postsData[] = [
                    'title' => $title,
                    'slug' => Str::slug($title) . '-' . Str::random(8),
                    'content' => $this->generateRealisticContent($faker, $category),
                    'category_id' => $categoryId,
                    'user_id' => $user->id,
                    'published_at' => $faker->dateTimeBetween('-2 years', 'now'),
                    'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
                    'updated_at' => now(),
                ];

                $progressBar->advance();

                // Insert in chunks to manage memory
                if (count($postsData) >= 500) {
                    foreach (array_chunk($postsData, 100) as $chunk) {
                        DB::table('posts')->insert($chunk);
                    }
                    $postsData = []; // Clear the array
                }
            }
        }

        // Insert any remaining posts
        if (count($postsData) > 0) {
            foreach (array_chunk($postsData, 100) as $chunk) {
                DB::table('posts')->insert($chunk);
            }
        }

        $progressBar->finish();
        $this->command->newLine(2);

        $this->command->info("✅ Successfully seeded " . ($allUsers->count() * 1000) . " realistic English posts!");
        $this->command->info("👥 Users: " . $allUsers->count());
        $this->command->info("📂 Categories: " . count($categories));
        $this->command->info("📊 Posts per user: 1000");
        
        $this->command->info("");
        $this->command->info("🎉 Seeding completed! You can add images later through:");
        $this->command->info("   - Your application's admin interface");
        $this->command->info("   - A separate image seeder");
        $this->command->info("   - Manual uploads");
    }

    /**
     * Generate realistic English content based on category
     */
    private function generateRealisticContent($faker, $category): string
    {
        $paragraphs = [];
        
        // Introduction paragraph
        $paragraphs[] = $faker->realText(200);
        
        // Middle paragraphs with more detailed content
        for ($i = 0; $i < 4; $i++) {
            $paragraphs[] = $faker->realText(300);
        }
        
        // Conclusion paragraph
        $paragraphs[] = $faker->realText(150);
        
        return implode("\n\n", $paragraphs);
    }

    /**
     * Modify titles to create variations
     */
    private function modifyTitle($title, $faker): string
    {
        $modifiers = [
            'A Comprehensive Guide to ',
            'Understanding ',
            'The Complete History of ',
            'Advanced Techniques in ',
            'Beginner\'s Guide to ',
            'The Science Behind ',
            'Practical Applications of ',
            'Future Trends in ',
            'Essential Tips for ',
            'Mastering the Art of ',
            'Innovative Approaches to ',
            'The Ultimate Guide to ',
            'Exploring the World of ',
            'Breaking Down ',
            'The Evolution of ',
        ];

        $suffixes = [
            ' in Modern Times',
            ' for Beginners',
            ' and Beyond',
            ': A Deep Dive',
            ' Explained',
            ' Made Simple',
            ' in 2024',
            ' and Future Prospects',
            ': What You Need to Know',
            ' from Experts',
        ];

        if ($faker->boolean(40)) {
            return $faker->randomElement($modifiers) . $title;
        } elseif ($faker->boolean(40)) {
            return $title . $faker->randomElement($suffixes);
        } else {
            return $title . ': ' . $faker->sentence(3);
        }
    }
}
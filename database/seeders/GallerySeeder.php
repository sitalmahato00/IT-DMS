<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $galleryItems = [
            [
                'title' => 'Campus Main Building',
                'description' => 'The main building of our institute featuring modern architecture and state-of-the-art facilities.',
                'image_path' => 'gallery/campus-1.jpg',
                'image_name' => 'campus-main.jpg',
                'category' => 'campus',
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Computer Lab',
                'description' => 'Fully equipped computer laboratory with the latest hardware and software.',
                'image_path' => 'gallery/campus-2.jpg',
                'image_name' => 'computer-lab.jpg',
                'category' => 'facilities',
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Annual Tech Fest',
                'description' => 'Students showcasing their innovative projects at the annual technical festival.',
                'image_path' => 'gallery/event-1.jpg',
                'image_name' => 'tech-fest.jpg',
                'category' => 'events',
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Sports Day',
                'description' => 'Annual sports day celebration with various athletic competitions.',
                'image_path' => 'gallery/activity-1.jpg',
                'image_name' => 'sports-day.jpg',
                'category' => 'activities',
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Graduation Ceremony',
                'description' => 'Proud graduates receiving their degrees at the annual convocation.',
                'image_path' => 'gallery/event-2.jpg',
                'image_name' => 'graduation.jpg',
                'category' => 'events',
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Library',
                'description' => 'Spacious library with a vast collection of books and digital resources.',
                'image_path' => 'gallery/campus-3.jpg',
                'image_name' => 'library.jpg',
                'category' => 'facilities',
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Freshers Welcome',
                'description' => 'New students being welcomed by seniors at the fresher orientation program.',
                'image_path' => 'gallery/activity-2.jpg',
                'image_name' => 'freshers-welcome.jpg',
                'category' => 'activities',
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Guest Lecture',
                'description' => 'Industry experts delivering insightful lectures to students.',
                'image_path' => 'gallery/event-3.jpg',
                'image_name' => 'guest-lecture.jpg',
                'category' => 'events',
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('galleries')->insert($galleryItems);
        
        $this->command->info('Gallery table seeded successfully with sample data!');
    }
}


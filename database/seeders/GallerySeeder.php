<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $galleries = [
            [
                'title' => 'Department Annual Event 2024',
                'description' => 'Photos from the annual departmental celebration',
                'category' => 'Events',
                'order' => 1,
            ],
            [
                'title' => 'Lab Setup and Equipment',
                'description' => 'Computer laboratory setup and equipment showcase',
                'category' => 'Facilities',
                'order' => 2,
            ],
            [
                'title' => 'Student Projects',
                'description' => 'Student project presentations and showcases',
                'category' => 'Academics',
                'order' => 3,
            ],
            [
                'title' => 'Faculty Team',
                'description' => 'Faculty members and staff of IT Department',
                'category' => 'People',
                'order' => 4,
            ],
            [
                'title' => 'Campus Views',
                'description' => 'Beautiful views of the college campus',
                'category' => 'Campus',
                'order' => 5,
            ],
        ];

        foreach ($galleries as $gallery) {
            $slug = Str::slug($gallery['title']);
            Gallery::firstOrCreate(
                ['title' => $gallery['title']],
                array_merge($gallery, [
                    'image_name' => 'gallery-' . $slug . '.jpg',
                    'image_path' => '/storage/gallery/' . $slug . '.jpg',
                    'is_active' => true,
                ])
            );
        }
    }
}

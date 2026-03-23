<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notice;
use App\Models\User;

class NoticeSeeder extends Seeder
{
    public function run(): void
    {
        $creatorId = User::query()->value('id');

        $notices = [
            [
                'title' => 'Welcome to New Academic Session 2081/082',
                'message' => 'Welcome all students and teachers. Please check your class schedules and subject assignments.',
                'audience' => 'all',
                'status' => 'published',
                'is_important' => true,
                'published_at' => now(),
                'created_by' => $creatorId,
            ],
            [
                'title' => 'Exam Schedule Published',
                'message' => 'Internal exam schedule has been published. Please check the exam section for dates and timings.',
                'audience' => 'students',
                'status' => 'published',
                'is_important' => false,
                'published_at' => now(),
                'created_by' => $creatorId,
            ],
            [
                'title' => 'Holiday Notice',
                'message' => 'College will remain closed during Dashain holidays. Regular classes will resume afterward.',
                'audience' => 'all',
                'status' => 'published',
                'is_important' => false,
                'published_at' => now(),
                'created_by' => $creatorId,
            ],
        ];

        foreach ($notices as $notice) {
            Notice::create($notice);
        }
    }
}


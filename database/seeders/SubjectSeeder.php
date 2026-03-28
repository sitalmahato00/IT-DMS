<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            // Semester 1 (8 subjects)
            ['subject_name' => 'Computer Fundamentals', 'subject_code' => 'CF101', 'semester' => '1', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Programming Fundamentals', 'subject_code' => 'PF102', 'semester' => '1', 'credits' => '4', 'subject_type' => 'core'],
            ['subject_name' => 'Mathematics I', 'subject_code' => 'M101', 'semester' => '1', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Communication English', 'subject_code' => 'CE101', 'semester' => '1', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Digital Logic', 'subject_code' => 'DL103', 'semester' => '1', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Essential Of Computing', 'subject_code' => 'EC104', 'semester' => '1', 'credits' => '2', 'subject_type' => 'core'],
            ['subject_name' => 'Physics I', 'subject_code' => 'PHY101', 'semester' => '1', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Environment and Technology', 'subject_code' => 'ET102', 'semester' => '1', 'credits' => '2', 'subject_type' => 'core'],
            
            // Semester 2 (8 subjects)
            ['subject_name' => 'Object Oriented Programming', 'subject_code' => 'OOP201', 'semester' => '2', 'credits' => '4', 'subject_type' => 'core'],
            ['subject_name' => 'Data Structures & Algorithm', 'subject_code' => 'DSA202', 'semester' => '2', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Mathematics II', 'subject_code' => 'M201', 'semester' => '2', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Computer Organization', 'subject_code' => 'CO204', 'semester' => '2', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Web Technology I', 'subject_code' => 'WT205', 'semester' => '2', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Applied Physics', 'subject_code' => 'AP202', 'semester' => '2', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Technical Communication', 'subject_code' => 'TC206', 'semester' => '2', 'credits' => '2', 'subject_type' => 'core'],
            ['subject_name' => 'Circuit Theory', 'subject_code' => 'CT207', 'semester' => '2', 'credits' => '3', 'subject_type' => 'core'],
            
            // Semester 3 (8 subjects)
            ['subject_name' => 'Database Management System', 'subject_code' => 'DBS301', 'semester' => '3', 'credits' => '4', 'subject_type' => 'core'],
            ['subject_name' => 'Software Engineering', 'subject_code' => 'SE302', 'semester' => '3', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Microprocessor', 'subject_code' => 'MP303', 'semester' => '3', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Web Technology II', 'subject_code' => 'WT304', 'semester' => '3', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Statistics', 'subject_code' => 'ST305', 'semester' => '3', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Mathematics III', 'subject_code' => 'M301', 'semester' => '3', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Professional Practice', 'subject_code' => 'PP306', 'semester' => '3', 'credits' => '2', 'subject_type' => 'core'],
            ['subject_name' => 'Signals & Systems', 'subject_code' => 'SS307', 'semester' => '3', 'credits' => '3', 'subject_type' => 'core'],
            
            // Semester 4 (8 subjects)
            ['subject_name' => 'Computer Network', 'subject_code' => 'CN401', 'semester' => '4', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Operating System', 'subject_code' => 'OS402', 'semester' => '4', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Artificial Intelligence', 'subject_code' => 'AI403', 'semester' => '4', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Digital Signal Processing', 'subject_code' => 'DSP404', 'semester' => '4', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'System Design', 'subject_code' => 'SD405', 'semester' => '4', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Information Security', 'subject_code' => 'IS406', 'semester' => '4', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Optical Communication', 'subject_code' => 'OC407', 'semester' => '4', 'credits' => '3', 'subject_type' => 'elective'],
            ['subject_name' => 'Mobile Application Development', 'subject_code' => 'MAD408', 'semester' => '4', 'credits' => '3', 'subject_type' => 'elective'],
            
            // Semester 5 (8 subjects)
            ['subject_name' => 'Machine Learning', 'subject_code' => 'ML501', 'semester' => '5', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Advanced Database', 'subject_code' => 'ADB502', 'semester' => '5', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Cloud Computing', 'subject_code' => 'CC503', 'semester' => '5', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Embedded Systems', 'subject_code' => 'ES504', 'semester' => '5', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Big Data Analytics', 'subject_code' => 'BDA505', 'semester' => '5', 'credits' => '3', 'subject_type' => 'elective'],
            ['subject_name' => 'Cybersecurity', 'subject_code' => 'CS506', 'semester' => '5', 'credits' => '3', 'subject_type' => 'elective'],
            ['subject_name' => 'IoT Systems', 'subject_code' => 'IOT507', 'semester' => '5', 'credits' => '3', 'subject_type' => 'elective'],
            ['subject_name' => 'Natural Language Processing', 'subject_code' => 'NLP508', 'semester' => '5', 'credits' => '3', 'subject_type' => 'elective'],
            
            // Semester 6 (8 subjects)
            ['subject_name' => 'Deep Learning', 'subject_code' => 'DL601', 'semester' => '6', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Distributed Systems', 'subject_code' => 'DS602', 'semester' => '6', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Blockchain Technology', 'subject_code' => 'BT603', 'semester' => '6', 'credits' => '3', 'subject_type' => 'elective'],
            ['subject_name' => 'DevOps & Containerization', 'subject_code' => 'DV604', 'semester' => '6', 'credits' => '3', 'subject_type' => 'elective'],
            ['subject_name' => 'Advanced Web Security', 'subject_code' => 'AWS605', 'semester' => '6', 'credits' => '3', 'subject_type' => 'elective'],
            ['subject_name' => 'Computer Vision', 'subject_code' => 'CV606', 'semester' => '6', 'credits' => '3', 'subject_type' => 'elective'],
            ['subject_name' => 'Quantum Computing', 'subject_code' => 'QC607', 'semester' => '6', 'credits' => '3', 'subject_type' => 'elective'],
            ['subject_name' => 'Project Work I', 'subject_code' => 'PW601', 'semester' => '6', 'credits' => '6', 'subject_type' => 'core'],
            
            // Semester 7 (6 subjects)
            ['subject_name' => 'Reinforcement Learning', 'subject_code' => 'RL701', 'semester' => '7', 'credits' => '3', 'subject_type' => 'elective'],
            ['subject_name' => 'Edge Computing', 'subject_code' => 'EC702', 'semester' => '7', 'credits' => '3', 'subject_type' => 'elective'],
            ['subject_name' => 'Advanced Networking', 'subject_code' => 'AN703', 'semester' => '7', 'credits' => '3', 'subject_type' => 'elective'],
            ['subject_name' => 'Game Development', 'subject_code' => 'GD704', 'semester' => '7', 'credits' => '3', 'subject_type' => 'elective'],
            ['subject_name' => 'Data Privacy & Ethics', 'subject_code' => 'DPE705', 'semester' => '7', 'credits' => '3', 'subject_type' => 'elective'],
            ['subject_name' => 'Project Work II', 'subject_code' => 'PW702', 'semester' => '7', 'credits' => '6', 'subject_type' => 'core'],
            
            // Semester 8 (5 subjects)
            ['subject_name' => 'Capstone Project', 'subject_code' => 'CP801', 'semester' => '8', 'credits' => '12', 'subject_type' => 'core'],
            ['subject_name' => 'Professional Ethics', 'subject_code' => 'PE802', 'semester' => '8', 'credits' => '2', 'subject_type' => 'core'],
            ['subject_name' => 'Entrepreneurship', 'subject_code' => 'ENT803', 'semester' => '8', 'credits' => '2', 'subject_type' => 'core'],
            ['subject_name' => 'Research Methodology', 'subject_code' => 'RM804', 'semester' => '8', 'credits' => '2', 'subject_type' => 'core'],
            ['subject_name' => 'Industrial Internship', 'subject_code' => 'II801', 'semester' => '8', 'credits' => '6', 'subject_type' => 'core'],
        ];

        foreach ($subjects as $subject) {
            Subject::updateOrCreate(
                ['subject_code' => $subject['subject_code']],
                array_merge($subject, ['status' => 'active'])
            );
        }
    }
}


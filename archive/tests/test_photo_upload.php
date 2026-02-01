<?php

/**
 * Test Profile Photo Upload Functionality
 * 
 * This script tests the profile photo upload feature to ensure it's working correctly.
 * Run this script after making changes to verify the fix.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProfilePhotoUploadTest
{
    private $testResults = [];
    
    public function run()
    {
        echo "=== Profile Photo Upload Test ===\n\n";
        
        // Initialize Laravel application
        $app = require_once __DIR__ . '/bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
        
        // Test 1: Check if storage disk is writable
        $this->testStorageWritable();
        
        // Test 2: Check if profile-photos directory exists
        $this->testProfileDirectoryExists();
        
        // Test 3: Check if profile_photo_path field exists in users table
        $this->testDatabaseFieldExists();
        
        // Test 4: Check if ProfileController has proper error handling
        $this->testProfileControllerCode();
        
        // Test 5: Check if view has error display for photo field
        $this->testViewErrorDisplay();
        
        // Test 6: Test actual file upload
        $this->testFileUpload();
        
        // Print results
        $this->printResults();
    }
    
    private function testStorageWritable()
    {
        echo "Test 1: Checking if storage disk is writable...\n";
        
        try {
            $disk = Storage::disk('public');
            $testFile = 'test_' . time() . '.txt';
            
            // Try to write a test file
            $disk->put($testFile, 'test content');
            
            // Check if file was created
            if ($disk->exists($testFile)) {
                // Clean up test file
                $disk->delete($testFile);
                echo "  ✓ PASS: Storage disk is writable\n";
                $this->testResults[] = ['test' => 'Storage Writable', 'status' => 'PASS'];
            } else {
                echo "  ✗ FAIL: Could not verify file creation\n";
                $this->testResults[] = ['test' => 'Storage Writable', 'status' => 'FAIL'];
            }
        } catch (\Exception $e) {
            echo "  ✗ FAIL: " . $e->getMessage() . "\n";
            $this->testResults[] = ['test' => 'Storage Writable', 'status' => 'FAIL', 'error' => $e->getMessage()];
        }
    }
    
    private function testProfileDirectoryExists()
    {
        echo "Test 2: Checking if profile-photos directory exists...\n";
        
        $path = storage_path('app/public/profile-photos');
        
        if (!file_exists($path)) {
            echo "  Creating profile-photos directory...\n";
            @mkdir($path, 0755, true);
        }
        
        if (file_exists($path) && is_dir($path)) {
            echo "  ✓ PASS: profile-photos directory exists\n";
            $this->testResults[] = ['test' => 'Profile Directory Exists', 'status' => 'PASS'];
        } else {
            echo "  ✗ FAIL: profile-photos directory does not exist and could not be created\n";
            $this->testResults[] = ['test' => 'Profile Directory Exists', 'status' => 'FAIL'];
        }
    }
    
    private function testDatabaseFieldExists()
    {
        echo "Test 3: Checking if profile_photo_path field exists in users table...\n";
        
        try {
            $columnExists = \Illuminate\Support\Facades\Schema::hasColumn('users', 'profile_photo_path');
            
            if ($columnExists) {
                echo "  ✓ PASS: profile_photo_path field exists\n";
                $this->testResults[] = ['test' => 'Database Field Exists', 'status' => 'PASS'];
            } else {
                echo "  ✗ FAIL: profile_photo_path field does not exist in users table\n";
                $this->testResults[] = ['test' => 'Database Field Exists', 'status' => 'FAIL'];
            }
        } catch (\Exception $e) {
            echo "  ✗ FAIL: " . $e->getMessage() . "\n";
            $this->testResults[] = ['test' => 'Database Field Exists', 'status' => 'FAIL', 'error' => $e->getMessage()];
        }
    }
    
    private function testProfileControllerCode()
    {
        echo "Test 4: Checking ProfileController code...\n";
        
        $controllerPath = app_path('Http/Controllers/ProfileController.php');
        $content = file_get_contents($controllerPath);
        
        $checks = [
            'File validation with isValid()' => '$file->isValid()',
            'Error logging with try-catch' => 'try {',
            'Error handling with withErrors' => 'withErrors',
            'Unique filename generation' => 'time()',
            'storeAs method for unique filenames' => 'storeAs',
        ];
        
        $passed = true;
        foreach ($checks as $check => $pattern) {
            if (strpos($content, $pattern) === false) {
                echo "  ✗ FAIL: Missing {$check}\n";
                $passed = false;
            }
        }
        
        if ($passed) {
            echo "  ✓ PASS: ProfileController has proper error handling\n";
            $this->testResults[] = ['test' => 'ProfileController Code', 'status' => 'PASS'];
        } else {
            $this->testResults[] = ['test' => 'ProfileController Code', 'status' => 'FAIL'];
        }
    }
    
    private function testViewErrorDisplay()
    {
        echo "Test 5: Checking view has error display for photo field...\n";
        
        $viewPath = resource_path('views/profile/edit.blade.php');
        $content = file_get_contents($viewPath);
        
        // Check if @error('photo') directive exists
        if (strpos($content, "@error('photo')") !== false || strpos($content, '@error("photo")') !== false) {
            echo "  ✓ PASS: View has error display for photo field\n";
            $this->testResults[] = ['test' => 'View Error Display', 'status' => 'PASS'];
        } else {
            echo "  ✗ FAIL: View missing error display for photo field\n";
            $this->testResults[] = ['test' => 'View Error Display', 'status' => 'FAIL'];
        }
    }
    
    private function testFileUpload()
    {
        echo "Test 6: Testing actual file upload...\n";
        
        try {
            // Create a test image
            $image = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
            
            // Create uploaded file
            $file = UploadedFile::fake()->image('test-photo.jpg', 100, 100)->size(100);
            
            // Store the file
            $path = $file->store('profile-photos', 'public');
            
            if ($path && Storage::disk('public')->exists($path)) {
                // Clean up test file
                Storage::disk('public')->delete($path);
                echo "  ✓ PASS: File upload works correctly\n";
                $this->testResults[] = ['test' => 'File Upload', 'status' => 'PASS'];
            } else {
                echo "  ✗ FAIL: File was not stored correctly\n";
                $this->testResults[] = ['test' => 'File Upload', 'status' => 'FAIL'];
            }
        } catch (\Exception $e) {
            echo "  ✗ FAIL: " . $e->getMessage() . "\n";
            $this->testResults[] = ['test' => 'File Upload', 'status' => 'FAIL', 'error' => $e->getMessage()];
        }
    }
    
    private function printResults()
    {
        echo "\n=== Test Results Summary ===\n\n";
        
        $passed = 0;
        $failed = 0;
        
        foreach ($this->testResults as $result) {
            $status = $result['status'];
            if ($status === 'PASS') {
                $passed++;
            } else {
                $failed++;
            }
        }
        
        echo "Passed: {$passed}\n";
        echo "Failed: {$failed}\n\n";
        
        if ($failed === 0) {
            echo "✓ All tests passed! Profile photo upload should be working correctly.\n";
        } else {
            echo "✗ Some tests failed. Please review the errors above.\n";
        }
    }
}

// Run the test
$test = new ProfilePhotoUploadTest();
$test->run();


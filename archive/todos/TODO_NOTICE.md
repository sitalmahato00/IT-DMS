# Notice Page Connection - TODO List

## Step 1: Create Notice Model
- [x] Create `app/Models/Notice.php` with fillable fields and relationships

## Step 2: Create NoticeController
- [x] Create `app/Http/Controllers/Admin/NoticeController.php`
- [x] Implement `index()` - List notices with pagination and filtering
- [x] Implement `store()` - Create new notice
- [x] Implement `update()` - Update existing notice
- [x] Implement `destroy()` - Delete notice
- [x] Implement `toggleStatus()` - Toggle publish/draft status
- [x] Implement `show()` - Get single notice details

## Step 3: Update Migration
- [x] Update `database/migrations/2026_01_28_000080_create_notices_table.php`
- [x] Add: `audience` column
- [x] Add: `status` column
- [x] Add: `semester` column
- [x] Add: `is_important` column
- [x] Add: `published_at` column

## Step 4: Update Routes
- [x] Update `routes/web.php` to use NoticeController
- [x] Add proper RESTful routes for CRUD operations

## Step 5: Update View
- [x] Update `resources/views/admin/notice-board.blade.php`
- [x] Make statistics dynamic from database
- [x] Make notice list dynamic with pagination
- [x] Add AJAX form submissions
- [x] Add proper forms with CSRF tokens

## Step 6: Testing
- [x] Run migration: `php artisan migrate`
- [x] Seed sample notices: `php artisan db:seed --class=NoticeSeeder`
- [ ] Test notice creation
- [ ] Test notice listing and pagination
- [ ] Test notice editing
- [ ] Test notice deletion


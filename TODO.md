# College Logo Fix TODO

## Status: [ ] In Progress

1. [x] Create TODO.md
2. [x] Fix JS preview in admin/department.blade.php (validation, preview clear, reload after save)
3. [x] Add cache-busting to Department model (timestamp ?v=)
4. [x] Enhance DepartmentController (cache flush)
5. [x] Routes confirmed OK
6. [ ] Execute: php artisan storage:link && php artisan cache:clear && php artisan view:clear
7. [ ] Test admin/department → upload/save → check login/landing logos update

**Notes:**
- Priority: JS preview first (immediate user feedback)
- Then server save + cache handling

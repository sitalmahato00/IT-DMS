out # Task: Update background image for forgot password page to public/images/loginbg.jpeg

## Steps:
- [x] Step 1: Convert resources/views/auth/forgot-password.blade.php to extend layouts/public.blade.php, add @push('head') @include('auth.partials.brand-theme'), replicate structure from login.blade.php/reset-password.blade.php with left hero/right form panels using the loginbg.jpeg background (already in brand-theme).
- [x] Step 2: Customize form for forgot-password: POST to route('password.email'), single email field, session status with countdown JS, error handling.
- [x] Step 3: Add bilingual support with $locale, $department vars matching other auth pages.
- [ ] Step 4: Test page in browser.
- [ ] Step 5: Mark complete and attempt_completion.

Addressing feedback:
- Steps 1-3 ✅ (forgot-password updated)
- Additional: Fix bg visibility on form panels for reset-password, forgot-password, register, login (make semi-transparent bg/form adjustments in brand-theme).
- Fix scrolling: decrease form size, no-scroll viewport lock.
- Step 4: Apply fixes.
Current progress: Implementing feedback fixes.

out # Task: Update background image for forgot password page to public/images/loginbg.jpeg

## Steps:
- [ ] Step 1: Convert resources/views/auth/forgot-password.blade.php to extend layouts/public.blade.php, add @push('head') @include('auth.partials.brand-theme'), replicate structure from login.blade.php/reset-password.blade.php with left hero/right form panels using the loginbg.jpeg background (already in brand-theme).
- [ ] Step 2: Customize form for forgot-password: POST to route('password.email'), single email field, session status with countdown JS, error handling.
- [ ] Step 3: Add bilingual support with $locale, $department vars matching other auth pages.
- [ ] Step 4: Test page in browser.
- [ ] Step 5: Mark complete and attempt_completion.

Current progress: Starting Step 1.


# Department Settings - Developer Quick Reference

## Quick Start

### 1. Access the Page
```
Route: /admin/department/edit
Controller: App\Http\Controllers\Admin\DepartmentController
Method: edit()
```

### 2. Update the Form
Post to: `admin.department.update`

### 3. Required Dependencies
- Tailwind CSS (v3.0+)
- Bootstrap Icons (`<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.css">`)
- Modern browser with ES6+ support

---

## File Structure

```
resources/
├── views/
│   └── admin/
│       ├── department.blade.php          ← Main view (ENHANCED)
│       └── department-enhanced.blade.php ← Backup version
├── css/
│   └── department-settings.css           ← Additional styling (optional)
└── js/                                   ← (if separated JS file)

app/
└── Http/
    └── Controllers/
        └── Admin/
            └── DepartmentController.php  ← Backend logic
```

---

## Key Components

### 1. Tab System
```html
<button class="tab-btn active" data-tab="basic">
    <i class="bi bi-file-earmark-text"></i>Basic Info
</button>
```

**JavaScript:**
```javascript
switchTab('basic')  // Programmatically switch tabs
```

### 2. Image Upload

**Logo Upload:**
```html
<input type="file" name="logo" accept="image/*" onchange="handleLogoUpload(event)">
```

**Handler:**
```javascript
function handleLogoUpload(event) {
    const file = event.target.files[0];
    // File validation and preview
}
```

**Deletion:**
```javascript
async function deleteLogo() {
    // API call to delete logo
}
```

### 3. Map Preview
```javascript
// Auto-updates when user changes:
// - Latitude/Longitude
// - Map Embed URL
// - Map Label

updateMapPreview()
```

**Supported Maps:**
- OpenStreetMap (from coordinates)
- Google Maps (from embed URL)

### 4. Form Submission
```javascript
document.getElementById('departmentForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    // Validation → FormData → Fetch → API call
});
```

---

## Common Customizations

### Change Primary Color
**Current:** Blue (#2563eb)

**To change:**
1. Update all `bg-blue-600` classes to your color
2. Update CSS variables if using custom CSS
3. Update gradient classes: `from-blue-600 to-blue-700`

### Add New Tab Section

**1. Add Tab Button:**
```html
<button class="tab-btn px-6 py-4 font-semibold text-gray-700 border-b-2 border-gray-200" 
        data-tab="new-section">
    <i class="bi bi-icon-name"></i>New Section
</button>
```

**2. Add Tab Content:**
```html
<div class="tab-content hidden" id="new-section-content">
    <!-- Your form fields here -->
</div>
```

**3. Add to JavaScript (if needed):**
```javascript
// The switchTab function already handles all tabs automatically
```

### Add Custom Validation

**In your form submission handler:**
```javascript
// Before: showLoader(true, 'Saving...')

if (customValidation() === false) {
    showToast('Custom error message', 'error');
    return;
}
```

### Modify Toast Notifications

**Current types:** success, error, info, warning

**Add new type:**
```javascript
const colors = {
    success: 'bg-gradient-to-r from-green-500 to-green-600',
    error: 'bg-gradient-to-r from-red-500 to-red-600',
    info: 'bg-gradient-to-r from-blue-500 to-blue-600',
    warning: 'bg-gradient-to-r from-yellow-500 to-yellow-600',
    custom: 'bg-gradient-to-r from-purple-500 to-purple-600',  // Add this
};
```

---

## API Endpoints

### Main Update Endpoint
```
POST /admin/department/update

Request: FormData
{
    "name": "string",
    "name_nepali": "string",
    "short_name": "string",
    "phone": "string",
    "email": "string",
    "website": "url",
    "address": "string",
    "address_nepali": "string",
    "city": "string",
    "district": "string",
    "province": "string",
    "latitude": "numeric",
    "longitude": "numeric",
    "map_embed_url": "string",
    "map_label": "string",
    "principal_name": "string",
    "principal_phone": "string",
    "principal_email": "string",
    "established_year": "integer",
    "registration_number": "string",
    "description": "string",
    "description_nepali": "string",
    "programs_title": "string",
    "programs_title_nepali": "string",
    "programs_content": "string",
    "programs_content_nepali": "string",
    "logo": "file",
    "hero_images[]": "files",
    "programs_image": "file"
}

Response:
{
    "success": true,
    "message": "Department details saved successfully",
    "data": { /* updated department data */ }
}
```

### Delete Logo
```
DELETE /admin/department/logo-delete

Response:
{
    "success": true,
    "message": "Logo deleted successfully"
}
```

### Delete Hero Image
```
DELETE /admin/department/hero-images/{index}

Response:
{
    "success": true,
    "message": "Hero image deleted successfully"
}
```

### Delete Programs Image
```
DELETE /admin/department/programs-image

Response:
{
    "success": true,
    "message": "Programs image deleted successfully"
}
```

---

## Database Fields

All department fields from the Migration/Model:

```
name                    string
name_nepali            string
short_name             string(10)
logo_path              string
hero_images            json/array
phone                  string(20)
email                  string
website                string
address                string(1000)
address_nepali         string(1000)
city                   string(100)
district               string(100)
province               string(100)
latitude               decimal(10,8)
longitude              decimal(11,8)
map_embed_url          string(2000)
map_label              string
principal_name         string
principal_phone        string(20)
principal_email        string
established_year       integer
registration_number    string(100)
description            string(2000)
description_nepali     string(2000)
programs_title         string
programs_title_nepali  string
programs_content       string(4000)
programs_content_nepali string(4000)
programs_image_path    string
```

---

## JavaScript Functions Reference

### Public API

```javascript
// Tab switching
switchTab(tabName)           // Switch to tab by name

// Notifications
showToast(message, type)     // Show toast notification
showLoader(show, text)       // Show/hide loader

// Image handlers
handleLogoUpload(event)                    // Process logo upload
deleteLogo()                              // Delete logo
deleteHeroImage(index)                    // Delete hero image by index
handleProgramsImageUpload(event)          // Process programs image
deleteProgramsImage()                     // Delete programs image

// Map
updateMapPreview()           // Update map preview display
```

---

## Styling Classes

### Form Fields
```html
<input class="field-input w-full px-4 py-3 ...">
```

### Upload Zones
```html
<div id="heroDropZone" class="border-2 border-dashed ...">
```

### Buttons
```html
<button class="bg-gradient-to-r from-blue-600 to-blue-700 ...">
```

### Animations
```css
.animate-slide-in   /* Toast slide in */
.animate-slide-out  /* Toast slide out */
.animate-fade-in    /* Fade in */
.animate-fade-out   /* Fade out */
```

---

## Performance Tips

1. **Lazy Load Images**
   - Use `loading="lazy"` on image previews
   - Compress images before upload

2. **Debounce Map Updates**
   - Current: Updates on change
   - Consider: Debounce with 500ms delay

3. **Cache Uploads**
   - Store FormData in session for recovery
   - Pre-validate files on client

4. **Optimize Bundle**
   - Tree-shake unused Bootstrap Icons
   - Compress CSS/JS in production

---

## Testing Checklist

- [ ] Logo upload and delete
- [ ] Hero images multiple upload
- [ ] Programs image upload
- [ ] Tab switching
- [ ] Map preview (coordinates)
- [ ] Map preview (embed URL)
- [ ] Form validation
- [ ] Form submission
- [ ] Toast notifications
- [ ] Responsive design (mobile/tablet)
- [ ] Keyboard navigation
- [ ] Screen reader compatibility
- [ ] Browser compatibility

---

## Debugging

### Check Browser Console
```javascript
// Enable debug logging
window.DEBUG = true;

// Check form data before submit
const form = document.getElementById('departmentForm');
console.log(new FormData(form));
```

### Common Issues

**Map not showing:**
```javascript
// Check if coordinates are valid
const lat = document.querySelector('input[name="latitude"]').value;
const lon = document.querySelector('input[name="longitude"]').value;
console.log(`Lat: ${lat}, Lon: ${lon}`); // Should be numbers
```

**Images not uploading:**
```javascript
// Check file size
const file = event.target.files[0];
console.log(`File size: ${file.size / 1024}KB`); // Calculate KB
```

**Form not submitting:**
```javascript
// Check CSRF token
const token = document.querySelector('meta[name="csrf-token"]').content;
console.log(`Token: ${token}`); // Should not be empty
```

---

## Maintenance

### Regular Tasks
- [ ] Monitor image storage usage
- [ ] Review error logs
- [ ] Test all upload functions
- [ ] Validate file permissions
- [ ] Check for deprecated code

### Monthly Review
- [ ] Check browser compatibility
- [ ] Review performance metrics
- [ ] Update dependencies
- [ ] Security audit

---

## Support Resources

- **Bootstrap Icons:** https://icons.getbootstrap.com/
- **Tailwind CSS:** https://tailwindcss.com/
- **Laravel Docs:** https://laravel.com/docs/
- **HTML Forms:** https://developer.mozilla.org/en-US/docs/Web/HTML/Element/form
- **File API:** https://developer.mozilla.org/en-US/docs/Web/API/File

---

## Related Files

- Controller: [DepartmentController.php](../../../app/Http/Controllers/Admin/DepartmentController.php)
- Model: [Department.php](../../../app/Models/Department.php)
- Routes: [web.php](../../../routes/web.php)
- Documentation: [DEPARTMENT_SETTINGS_GUIDE.md](../DEPARTMENT_SETTINGS_GUIDE.md)

---

**Version:** 1.0
**Last Updated:** March 29, 2026
**Author:** IT-DMS Enhancement
**Status:** Production Ready

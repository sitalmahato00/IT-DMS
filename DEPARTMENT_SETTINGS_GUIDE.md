# Enhanced Department Settings Page - Setup Guide

## Overview
This is a professional, modern Department Settings page for the IT-DMS admin panel. It provides a clean, intuitive interface for managing all department information with a tab-based layout.

## Features

### 1. **Tab-Based Navigation**
- **Basic Info**: Department logo, name (English/Nepali), and short name
- **Contact**: Phone, email, website, and full address
- **Location**: Coordinates (latitude/longitude), map embed, and live preview
- **Leadership**: HOD/Principal information
- **Details**: Established year, registration, and descriptions
- **Landing Page**: Hero images gallery and programs section

### 2. **Image Management**
- **Logo Upload**: Drag & drop or click to upload with instant preview
- **Hero Images**: Multiple image upload with gallery grid view
- **Programs Image**: Single image upload with 16:9 aspect ratio
- **Delete Functionality**: Remove images with confirmation
- **File Validation**: Size checks (2MB for logo, 4MB for images)

### 3. **Map Integration**
- **Live Map Preview**: Updates as you enter coordinates or embed URL
- **OpenStreetMap Support**: Automatic map generation from coordinates
- **Google Maps Support**: Accept embed URLs from Google Maps
- **Map Label**: Optional label that appears on the map
- **Coordinates Range Validation**: Latitude (-90 to 90), Longitude (-180 to 180)

### 4. **Bilingual Support**
- All major text fields support both English and Nepali
- Clear labels in both languages
- Helpful placeholders and descriptions
- Language-specific validation

### 5. **User Experience**
- **Smooth Transitions**: Animated tab switching with scroll-to-top
- **Toast Notifications**: Success, error, warning, and info messages
- **Loading States**: Global loader overlay during form submission
- **Form Validation**: Real-time field validation before submission
- **Drag & Drop**: Support for dragging files to upload areas
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile
- **Sticky Submit Buttons**: Easy access at any scroll position

### 6. **Professional Styling**
- Modern gradient header (blue theme)
- Card-based layout with shadows and rounded corners
- Tailwind CSS for responsive design
- Soft shadows and smooth transitions
- Consistent spacing and typography
- Color-coded icons for better UX

## Field Structure

### Basic Info Section
```
- Department Logo (image upload with preview)
- Department Name (English, required)
- Department Name (Nepali, required)
- Short Name (required, max 10 chars)
```

### Contact Information Section
```
- Phone (tel input)
- Email (email input with validation)
- Website (URL input)
- Address (English, textarea)
- Address (Nepali, textarea)
- City
- District
- Province
```

### Location & Map Section
```
- Latitude (number, -90 to 90)
- Longitude (number, -180 to 180)
- Map Embed URL (optional, supports Google Maps iframe)
- Map Label (optional, display on map)
- Live Map Preview (auto-updates)
```

### Leadership Section
```
- HOD/Principal Name
- HOD/Principal Phone
- HOD/Principal Email
```

### Department Details Section
```
- Established Year (number input)
- Registration Number
- Description (English, textarea)
- Description (Nepali, textarea)
```

### Landing Page Section
```
Hero Section:
- Multiple Images Upload (gallery with drag & drop)
- Existing Images Grid with delete buttons

Programs Section:
- Programs Title (English)
- Programs Title (Nepali)
- Programs Content (English)
- Programs Content (Nepali)
- Programs Image (single upload)
```

## Technical Implementation

### Frontend Technologies
- **Tailwind CSS**: Responsive styling
- **Vanilla JavaScript**: Tab switching, file handling, validation
- **Drag & Drop API**: File upload
- **FileReader API**: Image preview
- **Fetch API**: Form submission (AJAX)

### Backend Integration
- **Route**: `admin.department.update` (POST/PUT)
- **Validation**: All fields validated server-side
- **File Storage**: Public disk storage for images
- **CSRF Protection**: Token included in form

### JavaScript Functions

#### Tab Management
```javascript
switchTab(tabName)      // Switch to specific tab
                        // Shows content, updates UI
```

#### Image Handling
```javascript
handleLogoUpload(event)         // Process logo upload
deleteLogo()                     // Remove logo with API call
deleteHeroImage(index)           // Remove hero image
deleteProgramsImage()            // Remove programs image
handleProgramsImageUpload(event) // Process programs image
```

#### Map Preview
```javascript
updateMapPreview()      // Update map display
                        // Handles coordinates or embed URL
```

#### Notifications
```javascript
showToast(message, type)        // Display toast notification
showLoader(show, text)          // Show/hide loading overlay
```

## Responsive Design Breakdown

### Desktop (1024px+)
- 3-column grid for form fields
- Sticky header and navigation
- Full-width map preview
- Grid galleries for images

### Tablet (768px - 1023px)
- 2-column grid for form fields
- Optimized tab navigation
- Responsive map preview
- Adjusted image galleries

### Mobile (< 768px)
- Single column layout
- Scrollable horizontal tabs
- Touch-friendly file upload
- Optimized spacing

## Validation Rules

### Required Fields
- Department Name (English)
- Department Name (Nepali)
- Short Name

### Field-Specific Validation
- **Email**: Valid email format
- **Website**: Valid URL format
- **Latitude**: Number, -90 to 90
- **Longitude**: Number, -180 to 180
- **Established Year**: Number, 1900 to current year
- **Logo**: Image file, max 2MB
- **Hero Images**: Image files, max 4MB each
- **Programs Image**: Image file, max 4MB

## API Endpoints

### Main Endpoint
- `POST /admin/department/update` - Update department data

### Supporting Endpoints (if implemented)
- `DELETE /admin/department/logo-delete` - Remove logo
- `DELETE /admin/department/hero-images/{index}` - Remove hero image
- `DELETE /admin/department/programs-image` - Remove programs image

## Form Submission Flow

1. User clicks "Save Changes"
2. Form validation runs
3. Loading overlay appears
4. FormData is collected with all inputs
5. AJAX POST request sent to backend
6. Server validates all fields
7. Files are processed and stored
8. Database is updated
9. Success/error response returned
10. Toast notification shown
11. Page reloads on success

## CSS Classes & Styling

### Component Classes
```css
.tab-btn           /* Tab button styling */
.tab-content       /* Tab content container */
.field-input       /* Form input styling */
.animate-slide-in  /* Toast animation in */
.animate-slide-out /* Toast animation out */
```

### Tailwind Utilities
- `bg-gradient-to-r` - Header gradient
- `border-dashed` - Drop zones
- `shadow-lg` - Depth effect
- `rounded-xl` - Rounded corners
- `transition` - Smooth effects

## Browser Compatibility
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Dark Mode Considerations
The design currently uses light mode. For dark mode support, consider:
- Adding `dark:` class variants in Tailwind
- Storing user preference in localStorage
- Adjusting color schemes for dark backgrounds

## Performance Optimizations
- Lazy loading for images
- Debounced map preview updates
- Optimized file size validation
- CSS animations use GPU acceleration
- Minimized JavaScript DOM manipulation

## Accessibility Features
- Semantic HTML structure
- Proper label-input associations
- ARIA attributes for interactive elements
- Keyboard navigation support
- Focus indicators on form fields
- Screen reader friendly

## Future Enhancement Ideas

### Short Term
- Add rich text editor for descriptions
- Implement image cropping tool
- Add preview mode for landing page
- Section-wise auto-save drafts

### Medium Term
- Add multilingual language toggle (English/Nepali)
- Implement image compression
- Add URL slug generator for website
- Create department template system

### Long Term
- Real-time collaboration features
- Audit log for all changes
- Version history and rollback
- Advanced analytics integration
- Mobile app integration

## Troubleshooting

### Images Not Uploading
- Check file size (under limit)
- Verify MIME type
- Ensure storage directory is writable
- Check CSRF token is valid

### Map Not Displaying
- Verify coordinates are valid
- Check embed URL is valid iframe
- Ensure browser allows iframes
- Check browser console for errors

### Form Not Submitting
- Check browser network tab
- Verify CSRF token
- Check server logs
- Ensure all required fields filled

### Styling Issues
- Clear browser cache
- Verify Tailwind CSS is loaded
- Check for CSS conflicts
- Test in incognito mode

## Configuration Files

### Routes (routes/web.php)
```php
Route::group(['middleware' => 'auth:admin'], function () {
    Route::get('/admin/department/edit', [DepartmentController::class, 'edit'])->name('admin.department.edit');
    Route::post('/admin/department/update', [DepartmentController::class, 'update'])->name('admin.department.update');
    // Additional delete routes as needed
});
```

### Controller Methods
The existing `DepartmentController` handles:
- Validation of all inputs
- File upload and storage
- Database updates
- Response formatting

## File Sizes & Limits
- Logo: 2 MB max
- Hero Images: 4 MB max per image
- Programs Image: 4 MB max
- Total description length: 2000 characters
- Textarea fields: Configurable via HTML attributes

## Support & Maintenance

### Regular Maintenance
- Monitor storage usage
- Clean up orphaned images
- Update dependencies
- Review error logs

### Updates & Changes
- Test new features on staging
- Backup database before major changes
- Document all modifications
- Update this guide accordingly

---

**Last Updated**: March 29, 2026
**Version**: 1.0 (Enhanced Professional Edition)
**Status**: Production Ready

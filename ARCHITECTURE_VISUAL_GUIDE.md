# Component Architecture & Visual Guide

## Enhanced Department Settings - Complete Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                    DEPARTMENT SETTINGS PAGE                      │
│                                                                   │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │  Header Section (Gradient Blue)                            │  │
│  │  ■ Title: "Department Settings"                            │  │
│  │  ■ Subtitle: "Manage your department information..."       │  │
│  │  ■ Back Button                                             │  │
│  └───────────────────────────────────────────────────────────┘  │
│                                                                   │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │  Tab Navigation (Sticky)                                  │  │
│  │  [Basic] [Contact] [Location] [Leader] [Details] [Landing]  │  │
│  └───────────────────────────────────────────────────────────┘  │
│                                                                   │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │                    TAB CONTENT AREA                         │  │
│  │                                                               │  │
│  │  ▼ TAB 1: BASIC INFO                                        │  │
│  │  ┌─────────────────────────────────┐                       │  │
│  │  │  Logo Upload    │ Dept Name     │                       │  │
│  │  │  (Drag & Drop)  │ Dept Name (NP)│                       │  │
│  │  │  + Preview      │ Short Name    │                       │  │
│  │  └─────────────────────────────────┘                       │  │
│  │                                                               │  │
│  │  ▼ TAB 2: CONTACT                                           │  │
│  │  ┌─────────────┬─────────────┬──────────────┐             │  │
│  │  │ Phone       │ Email       │ Website      │             │  │
│  │  ├─────────────┴─────────────┴──────────────┤             │  │
│  │  │ Address (EN)  │ Address (NP)             │             │  │
│  │  ├─────────────┬─────────────┬──────────────┤             │  │
│  │  │ City        │ District    │ Province     │             │  │
│  │  └─────────────┴─────────────┴──────────────┘             │  │
│  │                                                               │  │
│  │  ▼ TAB 3: LOCATION                                          │  │
│  │  ┌──────────────┬──────────────┐                           │  │
│  │  │ Latitude     │ Longitude    │                           │  │
│  │  ├──────────────┴──────────────┤                           │  │
│  │  │ Map Embed URL (optional)    │                           │  │
│  │  ├─────────────────────────────┤                           │  │
│  │  │ Map Label (optional)        │                           │  │
│  │  ├─────────────────────────────┤                           │  │
│  │  │   MAP PREVIEW (LIVE)        │                           │  │
│  │  │   ┌─────────────────────┐   │                           │  │
│  │  │   │  Map renders here   │   │                           │  │
│  │  │   │  Updates as you     │   │                           │  │
│  │  │   │  enter coords/URL   │   │                           │  │
│  │  │   └─────────────────────┘   │                           │  │
│  │  └─────────────────────────────┤                           │  │
│  │                                                               │  │
│  │  ▼ TAB 4: LEADERSHIP                                        │  │
│  │  ┌──────────────┬──────────────┬──────────────┐            │  │
│  │  │ Name         │ Phone        │ Email        │            │  │
│  │  └──────────────┴──────────────┴──────────────┘            │  │
│  │                                                               │  │
│  │  ▼ TAB 5: DETAILS                                           │  │
│  │  ┌──────────────┬──────────────┐                           │  │
│  │  │ Est. Year    │ Reg. Number  │                           │  │
│  │  ├──────────────┴──────────────┤                           │  │
│  │  │ Description (EN)            │                           │  │
│  │  │ ┌──────────────────────────┐│                           │  │
│  │  │ │  Textarea               ││                           │  │
│  │  │ └──────────────────────────┘│                           │  │
│  │  ├──────────────────────────────┤                           │  │
│  │  │ Description (NP)            │                           │  │
│  │  │ ┌──────────────────────────┐│                           │  │
│  │  │ │  Textarea               ││                           │  │
│  │  │ └──────────────────────────┘│                           │  │
│  │  └──────────────────────────────┘                           │  │
│  │                                                               │  │
│  │  ▼ TAB 6: LANDING PAGE                                      │  │
│  │  ┌──────────────────────────────┐                           │  │
│  │  │ Hero Images                  │                           │  │
│  │  │ ┌────────┬────────┬────────┐ │                           │  │
│  │  │ │ [IMG] │ [IMG] │ [IMG] │  │ (Drag & Drop Area)        │  │
│  │  │ └────────┴────────┴────────┘ │                           │  │
│  │  ├──────────────────────────────┤                           │  │
│  │  │ Programs Section             │                           │  │
│  │  │ ┌────────────┬────────────┐   │                           │  │
│  │  │ │Title (EN)  │ Title (NP) │   │                           │  │
│  │  │ ├────────────┴────────────┤   │                           │  │
│  │  │ │Content (EN) │Content(NP)│   │                           │  │
│  │  │ ├─────────────────────────┤   │                           │  │
│  │  │ │  Programs Image Upload  │   │                           │  │
│  │  │ │  ┌─────────────────────┐│   │                           │  │
│  │  │ │  │   16:9 Preview    ││   │                           │  │
│  │  │ │  └─────────────────────┘│   │                           │  │
│  │  │ └─────────────────────────┘   │                           │  │
│  │  └──────────────────────────────┘                           │  │
│  │                                                               │  │
│  └───────────────────────────────────────────────────────────┘  │
│                                                                   │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │  Footer / Submit Buttons (Sticky)                          │  │
│  │  [💾 Save Changes]                        [✕ Cancel]      │  │
│  └───────────────────────────────────────────────────────────┘  │
│                                                                   │
│  ┌─── Toast Notifications (Top Right) ───┐                      │
│  │ ✓ Success message                      │                      │
│  └────────────────────────────────────────┘                      │
│                                                                   │
│  ┌─── Loading Overlay (Center) ───┐                             │
│  │ ⟳ Saving department details... │                             │
│  └────────────────────────────────┘                              │
└─────────────────────────────────────────────────────────────────┘
```

---

## Data Flow Diagram

```
┌────────────────┐
│  User Browser  │
└────────┬───────┘
         │
         ├─────────────────────────────────┐
         │                                  │
         ▼                                  ▼
    ┌─────────────────────┐       ┌──────────────────┐
    │  Form Inputs        │       │  File Uploads    │
    │                     │       │                  │
    │ • Text fields       │       │ • Logo           │
    │ • Email, URL        │       │ • Hero images    │
    │ • Coordinates       │       │ • Programs img   │
    │ • Textareas         │       │                  │
    │ • Selects           │       │                  │
    └──────────┬──────────┘       └────────┬─────────┘
               │                          │
               └───────────┬──────────────┘
                           │
                    ┌──────▼────────┐
                    │ Client Validation
                    │ • Required fields
                    │ • File sizes
                    │ • Formats
                    └──────┬────────┘
                           │
                      [VALID?] NO → ▼ Error Toast
                           │
                          YES
                           │
                    ┌──────▼────────────┐
                    │ FormData Collection│
                    │ + CSRF Token       │
                    └──────┬────────────┘
                           │
                      ┌────▼─────────────────┐
                      │ AJAX POST Request   │
                      │ to Server            │
                      └────┬────────┬───────┘
                           │        │
                           │        └─► [🔒 CSRF Check] ──── ✗ Fail
                           │
                      ┌────▼──────────────┐
                      │ Server Validation  │
                      │ • Email format     │
                      │ • URL format       │
                      │ • Coordinates      │
                      │ • File validation  │
                      └────┬──────┬───────┘
                           │      │
                      [VALID?]   ✗ ──────→ Error Response
                           │               │
                          YES              ▼
                           │         Error Toast Alert
                      ┌────▼──────────────┐
                      │ File Processing    │
                      │ • Validate mime    │
                      │ • Check size       │
                      │ • Store in public  │
                      │ • Get paths        │
                      └────┬──────────────┘
                           │
                      ┌────▼──────────────┐
                      │ Database Update    │
                      │ • Save all data    │
                      │ • Update records   │
                      │ • Commit changes   │
                      └────┬──────────────┘
                           │
                      ┌────▼──────────────┐
                      │ Success Response   │
                      │ • 200 OK           │
                      │ • Updated data     │
                      └────┬──────────────┘
                           │
                           ▼
                    ┌──────────────────┐
                    │ Success Toast    │
                    │ Page Reload      │
                    └──────────────────┘
```

---

## Component Hierarchy

```
DepartmentSettingsPage
│
├── Header Section
│   ├── Title
│   ├── Subtitle
│   └── Back Button
│
├── Tab Navigation
│   ├── Tab Button: Basic
│   ├── Tab Button: Contact
│   ├── Tab Button: Location
│   ├── Tab Button: Leader
│   ├── Tab Button: Details
│   └── Tab Button: Landing
│
├── Form Container
│   │
│   ├── Tab Content: BASIC
│   │   ├── Logo Upload Box
│   │   │   ├── Drag & Drop Area
│   │   │   ├── File Input
│   │   │   ├── Image Preview
│   │   │   └── Delete Button
│   │   ├── Department Name (EN)
│   │   ├── Department Name (NP)
│   │   └── Short Name
│   │
│   ├── Tab Content: CONTACT
│   │   ├── Phone Input
│   │   ├── Email Input
│   │   ├── Website Input
│   │   ├── Address (EN)
│   │   ├── Address (NP)
│   │   ├── City Input
│   │   ├── District Input
│   │   └── Province Input
│   │
│   ├── Tab Content: LOCATION
│   │   ├── Latitude Input
│   │   ├── Longitude Input
│   │   ├── Map Embed URL
│   │   ├── Map Label
│   │   └── Map Preview Area
│   │
│   ├── Tab Content: LEADER
│   │   ├── HOD Name
│   │   ├── HOD Phone
│   │   └── HOD Email
│   │
│   ├── Tab Content: DETAILS
│   │   ├── Established Year
│   │   ├── Registration Number
│   │   ├── Description (EN)
│   │   └── Description (NP)
│   │
│   └── Tab Content: LANDING
│       ├── Hero Images Section
│       │   ├── Upload Zone (Drag & Drop)
│       │   ├── File Input
│       │   └── Image Gallery
│       │       ├── Image Card 1
│       │       │   ├── Image
│       │       │   └── Delete Button
│       │       └── Image Card N...
│       └── Programs Section
│           ├── Title (EN) Input
│           ├── Title (NP) Input
│           ├── Content (EN) Textarea
│           ├── Content (NP) Textarea
│           ├── Image Upload
│           ├── Image Preview Area
│           └── Delete Button
│
├── Submit Section
│   ├── Save Button
│   └── Cancel Button
│
├── Toast Container
│   └── Toast Messages (Dynamic)
│
├── Loading Overlay
│   ├── Spinner
│   └── Status Text
│
└── Form Element
    └── CSRF Token (Hidden)
```

---

## Tab Navigation Flow

```
┌─────────────────────────┐
│   User Clicks Tab       │
└────────────┬────────────┘
             │
             ▼
      ┌──────────────┐
      │ switchTab()  │
      └──┬───────────┘
         │
         ├─ Hide all .tab-content
         │
         ├─ Deactivate all .tab-btn
         │  (remove blue border)
         │
         ├─ Show selected content
         │
         ├─ Activate selected button
         │  (add blue border)
         │
         └─ Scroll to top
              smoothly
```

---

## Image Upload Lifecycle

```
User Selects File
      │
      ▼
File Input Change Event
      │
      ├─ Get file from input
      │
      ├─ Validate size
      │  ├─ Logo: ≤ 2MB ✓
      │  ├─ Hero: ≤ 4MB ✓
      │  └─ Programs: ≤ 4MB ✓
      │
      ├─ Show error if invalid
      │
      └─ Create FileReader
         │
         ├─ Read as DataURL
         │
         ▼
      Display Preview
      (img tag with data URL)
      │
      ▼
  User Submits Form
      │
      ├─ FormData includes file
      │
      ├─ Send to server
      │
      └─ Server processes:
         ├─ Validate MIME type
         ├─ Check file size
         ├─ Move to storage
         ├─ Get storage path
         ├─ Update database
         └─ Return success
```

---

## Form Submission Sequence

```
1. User fills form fields
   ├─ Text inputs
   ├─ Textarea
   ├─ File uploads
   └─ Coordinates

2. Click "Save Changes"
   │
   └─→ Form submit handler

3. Validate required fields
   ├─ Check name (EN) ✓
   ├─ Check name (NP) ✓
   ├─ Check short name ✓
   └─ All required filled?
      ├─ NO: Show error → STOP
      └─ YES: Continue

4. Collect FormData
   ├─ All inputs
   ├─ All files
   ├─ CSRF token
   └─ Ready for submission

5. Show loading overlay

6. Send AJAX POST
   ├─ URL: /admin/department/update
   ├─ Method: POST
   ├─ Body: FormData
   └─ Headers: CSRF token

7. Server processes
   ├─ Validate inputs
   ├─ Process files
   ├─ Update database
   └─ Return JSON response

8. Check response
   ├─ Success?
   │  ├─ YES: Show success toast
   │  │      → Reload page
   │  └─ NO: Show error toast
   │         → Display message
   │         → Hide loader
   └─ Handle errors

9. Page reloads
   └─ Fresh data loaded
```

---

## State Management

```
┌──────────────────────────────┐
│    Current Tab               │
│  Default: 'basic'            │
│  Changes on: Tab click       │
│  Effects: Content shown/hide │
└──────────────────────────────┘

┌──────────────────────────────┐
│    Loading State             │
│  Default: false              │
│  Changes on: Form submit     │
│  Effects: Overlay visible    │
└──────────────────────────────┘

┌──────────────────────────────┐
│    Image Previews            │
│  Logo: Single preview        │
│  Heroes: Array of previews   │
│  Programs: Single preview    │
└──────────────────────────────┘

┌──────────────────────────────┐
│    Form Data                 │
│  In: All input values        │
│  Processing: FormData()      │
│  Out: JSON response          │
└──────────────────────────────┘

┌──────────────────────────────┐
│    Notifications             │
│  Toast messages              │
│  Auto-dismiss: 2.5s          │
│  Animations: Slide in/out    │
└──────────────────────────────┘
```

---

## Color & Design System

```
┌─ Brand Colors ─────────────────┐
│ Primary: #2563eb (Blue)         │
│ Hover: #1d4ed8 (Darker Blue)    │
│ Light: #dbeafe (Light Blue)     │
└─────────────────────────────────┘

┌─ Status Colors ─────────────────┐
│ Success: #10b981 (Green)        │
│ Error: #ef4444 (Red)            │
│ Warning: #f59e0b (Amber)        │
│ Info: #3b82f6 (Blue)            │
└─────────────────────────────────┘

┌─ Neutral Colors ────────────────┐
│ Text: #111827 (Near Black)      │
│ Border: #e5e7eb (Light Gray)    │
│ Background: #f9fafb (Off-White) │
│ White: #ffffff                  │
└─────────────────────────────────┘

┌─ Typography ────────────────────┐
│ Headers: Font Weight 600-700    │
│ Body: Font Weight 400-500       │
│ Sizes: 12px to 32px             │
└─────────────────────────────────┘

┌─ Spacing ───────────────────────┐
│ Small: 0.5rem (8px)             │
│ Medium: 1rem (16px)             │
│ Large: 1.5rem (24px)            │
│ X-Large: 2rem (32px)            │
└─────────────────────────────────┘

┌─ Effects ───────────────────────┐
│ Shadows: sm, md, lg, xl         │
│ Radius: 0.5-1rem                │
│ Transitions: 0.3s ease          │
│ Animations: Smooth              │
└─────────────────────────────────┘
```

---

## Mobile Responsiveness

```
Desktop (1024px+)
┌─────────────────────────┐
│ 3-4 Column Layout       │
│ All features visible    │
│ Horizontal tabs         │
└─────────────────────────┘

Tablet (768-1023px)
┌─────────────────────────┐
│ 2 Column Layout         │
│ Tab scroll horizontal   │
│ Optimized spacing       │
└─────────────────────────┘

Mobile (< 768px)
┌─────────────────────────┐
│ 1 Column Stack          │
│ Full-width inputs       │
│ Larger touch targets    │
│ Vertical tabs scroll    │
└─────────────────────────┘
```

---

## Accessibility Features

```
✓ Semantic HTML
  ├─ Proper heading hierarchy
  ├─ Form labels connected
  └─ ARIA attributes

✓ Keyboard Navigation
  ├─ Tab through inputs
  ├─ Tab button switching
  └─ Submit with Enter

✓ Screen Reader Support
  ├─ Descriptive labels
  ├─ Field descriptions
  └─ Error messages

✓ Visual Indicators
  ├─ Focus rings
  ├─ Color contrast
  └─ Large touch targets

✓ Reduced Motion
  ├─ Respects prefers-reduced-motion
  └─ Minimal animations
```

---

**Architecture Version:** 1.0
**Last Updated:** March 29, 2026
**Status:** Production Ready

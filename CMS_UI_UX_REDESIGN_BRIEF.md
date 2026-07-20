# CMS UI/UX Redesign Brief

Prepared after reviewing the current Laravel project in `D:\IT-DMS`, the current admin/HOD-style UI, the existing route and Blade structure, and the reference landing-page screenshot provided by the user.

## 1. New Product Name and Scope

System name:

- `CMS`
- Full meaning: `College Management System`

New product scope:

- The system is no longer only an IT department DMS
- It must support the full college and multiple departments
- It must support college-level administration and department-level administration
- It must keep the current HOD/admin experience familiar, but extend it with more features
- It must include a stronger public website, a new landing page, and a dedicated alumni media experience

Core platform idea:

- One college-wide CMS
- Many departments under the same system
- Shared design system
- Shared technical platform
- Department-aware content and permissions

## 2. Core Functional Direction

CMS should handle:

- Public college website
- Department pages and department-specific content
- Principal dashboard and college-wide administration
- HOD dashboard and department-level administration
- Teachers, students, parents, and alumni portals
- Notices, events, downloads, media, gallery, and academic content
- Academic operations like attendance, timetable, exams, marks, and reports

## 3. Required Role Model

### Principal / central admin

This is the new top-level admin dashboard.

Responsibilities:

- View the overall college status
- Monitor all departments
- Compare department performance
- Publish college-wide announcements
- Approve key department content
- Review admissions, academics, attendance, exam results, and media activity
- Access cross-department reports

### HOD / department admin

This should keep a UI similar to the current admin experience.

Requirements:

- Keep the current admin/HOD visual familiarity
- Keep similar navigation, tables, cards, and working style
- Extend with more modified features
- Scope content and operations to a selected department
- Allow department-specific media, notices, resources, and academic operations

### Other roles

- Teacher
- Student
- Parent
- Alumni
- Public guest

## 4. Multi-Department CMS Model

The new CMS must support:

- Multiple departments under one college
- Shared college identity with department-level branding blocks
- Department switcher for privileged users
- College-wide content and department-specific content
- College-level reports and department-level reports

Data and UI rules:

- Principal sees all departments
- HOD sees only their department
- Public landing can show the whole college plus featured departments
- Notices, downloads, and media can be tagged as:
  - college-wide
  - department-specific
  - alumni-specific

## 5. Existing Platform Strengths to Preserve

- Laravel Blade + Tailwind + Alpine foundation is fine for this CMS
- Role separation already exists
- Academic modules already exist
- Mobile shell and PWA support already exist
- Nepali and English support already exists
- Current HOD/admin interaction model is already usable

## 6. Main UX Problems to Fix

- UI is currently fragmented across roles
- Large inline styling is repeated in layouts and pages
- Dashboards are more decorative than structured
- Public website does not yet feel like a full college CMS landing page
- Current admin works, but it needs broader college-level features
- Alumni media experience is missing as a strong dedicated section
- The red identity exists, but the system still lacks one unified red-led design language

## 7. New CMS Design Direction

### Overall theme

The new CMS should feel like:

- A solid college platform
- Professional and institutional
- Clean and modern
- Fast and lightweight
- Red-first brand identity

### Color direction

All system color should be based on red.

Rules:

- Red is the primary brand color across the full system
- Backgrounds should still use white, off-white, and neutral surfaces for readability
- Red should drive:
  - top bars
  - primary buttons
  - active navigation
  - tabs
  - section headers
  - key badges
  - chart accents
- Do not make the entire UI a red block
- Use red as the dominant visual identity with neutral support colors

Suggested palette:

- Primary red: `#B5121B`
- Deep red: `#8F0E16`
- Bright red accent: `#D71920`
- Soft red background: `#FDEBEC`
- Surface: `#FFFFFF`
- Soft surface: `#F7F7F8`
- Ink: `#1C1F23`
- Muted text: `#6B7280`
- Border: `#E5E7EB`

## 8. CMS Experience Architecture

### Public experience

- New college landing page
- Department listing and department entry points
- Notices
- Downloads
- Faculty / people
- News and events
- Gallery / media
- Research / publications
- Alumni media page
- Contact and admissions

### Principal admin experience

- College overview dashboard
- Department performance overview
- Notices and announcements
- Media and website approvals
- Institution-level reports
- Cross-department resource and academic tracking

### HOD admin experience

- Similar to current admin UI
- Same working familiarity
- Better information hierarchy
- More department management tools
- Department content publishing
- Department-level media and alumni updates

## 9. Principal Dashboard Requirements

The new principal dashboard must support:

- College-wide KPI overview
- Total students, teachers, parents, departments, programs, and alumni counts
- Department cards with quick status
- Attendance summary by department
- Result / pass-rate summary by department
- Notice and content publishing overview
- Pending approvals
- Website content activity
- Media and alumni highlights
- Important alerts

Suggested dashboard blocks:

1. College summary header
2. Core KPI cards
3. Department performance grid
4. Academic health section
5. Content and media activity
6. Recent alerts and approvals
7. Quick actions

## 10. HOD Admin Direction

The HOD/admin portal should stay visually close to the current admin UI.

Keep similar:

- Sidebar logic
- Page header behavior
- Table-heavy workflow
- Filters and CRUD pattern
- Operational dashboard style

Improve with:

- Better spacing and hierarchy
- More consistent cards and panels
- Stronger department context
- Better mobile tables
- Cleaner forms
- Better batch actions
- More content-management capability
- Easier media and alumni publishing

New HOD features to add:

- Department homepage content management
- Department hero/banner management
- Department notices and events management
- Department media center
- Alumni media/story management per department
- Department-specific downloads and resources
- Department content approval workflow if required

## 11. New Landing Page Direction

The landing page should move from a department portal feel to a full college CMS landing page.

The screenshot provided should be used as the structural reference, but rebuilt in a cleaner, lighter, red-based design.

### Landing page structure based on the screenshot

1. Top utility strip

- Date / quick info
- Quick action buttons
- Possibly check mail / portal access / emergency notice

2. Main header

- College logo
- College name in English and Nepali
- Search
- Primary utility actions

3. Main navigation bar

- About Us
- Notices
- Schools / Departments
- Downloads
- People
- News & Events
- Research
- Gallery / Media
- Admission
- Contact

4. Large hero banner

- Wide campus image or slideshow
- Strong red overlay accents
- College-level message and CTA

5. Leadership + welcome section

- Chancellor / principal / vice-chancellor or equivalent leaders
- College welcome message
- About college CTA

6. Schools / departments card row

- Card-based entry points for major schools or departments
- Each card should open a department landing page

7. Notices and exam/result tab block

- Notice board tab
- Exam and result tab
- Fast scannable list
- View more CTA

8. News and events block

- Featured event panel
- Side list of recent events
- Media-rich but optimized

9. Downloads + official message block

- Download center widget
- Principal / official message card

10. Quick CTA strip

- Publications
- Scholarships
- Downloads
- Alumni

11. Program / course / blog section

- Bachelor programs
- Master programs
- College blog / updates

12. Alumni media preview section

- Featured alumni stories
- Reunion gallery
- Notable alumni
- Alumni news CTA

13. Footer

- Logo
- Quick links
- Contact information
- Map
- Social media

### Landing page visual rules

- Use the screenshot structure as inspiration, not a direct clone
- Convert the overall color identity to red instead of blue
- Use fewer heavy boxes and sharper hierarchy
- Keep the page image-driven but optimized
- Make it fast and mobile-friendly
- Keep major public content blocks modular and editable from CMS

## 12. Alumni Media Page

A new alumni-focused media section must be added.

Purpose:

- Showcase alumni success and memories
- Keep alumni connected to the college
- Support batches, reunions, stories, and achievements

Suggested alumni media page structure:

1. Alumni hero

- Strong heading
- Alumni association intro
- CTA to submit story or memory

2. Featured alumni stories

- Large cards with photo, title, department, batch, and summary

3. Alumni gallery / media wall

- Reunion photos
- Video highlights
- Event media

4. Batch memories

- Filter by year / batch / department

5. Notable alumni

- Achievement spotlight
- Industry / government / academic success stories

6. Alumni news and announcements

- Meetups
- Reunions
- Awards
- Contributions

7. Submission / contact block

- Invite alumni to contribute memories, stories, or media

### Alumni admin support

Admin side should support:

- Alumni media upload
- Story publishing
- Batch tagging
- Department tagging
- Featured/unfeatured control
- Approval status

## 13. CMS Page Inventory to Support

### Public

- Home / landing
- About college
- Departments / schools listing
- Department detail pages
- Notices
- Downloads
- People / faculty
- News and events
- Research
- Gallery / media
- Alumni media
- Contact
- Admission

### Principal admin

- Dashboard
- Department oversight
- Reports
- Content approvals
- Website media control
- College-wide notices

### HOD admin

- Current admin-like dashboard
- Students
- Teachers
- Parents
- Courses / subjects
- Semesters
- Attendance
- Exams
- Marks
- Timetable
- Reports
- Department website content
- Department media and alumni content

### Existing portals to keep

- Teacher
- Student
- Parent
- Auth flows
- Print and export flows

## 14. UI Pattern Strategy

### Shared shell

All authenticated CMS areas should use one shared shell:

- Sticky top header
- Left desktop sidebar
- Mobile bottom navigation
- Global search
- Notifications
- Reusable page header

### Shared page patterns

- Dashboard pattern
- Directory/list pattern
- Detail/profile pattern
- Form pattern
- Content manager pattern
- Media library pattern
- Report/print pattern

### Similarity rule for HOD admin

- Keep the current admin mental model
- Upgrade the system, do not replace it with a totally different style
- Principal dashboard can be more executive
- HOD dashboard should feel like the current working environment, only cleaner and broader

## 15. Performance Direction

CMS must remain lightweight and high-performance.

Current technical findings:

- `resources/css/app.css` is large and shared globally
- `resources/js/app.js` loads `Chart.js` and `jquery` broadly
- Layouts contain duplicated UI logic and large inline styles

Required improvements:

- Create shared design tokens
- Move repeated inline styles into reusable component CSS
- Lazy-load chart logic only where charts exist
- Lazy-load Nepali date picker only where date fields exist
- Reduce duplicate layout scripts
- Keep public pages especially light
- Optimize gallery and alumni media assets aggressively

## 16. Recommended CMS Modules

- College website CMS
- Department CMS
- Principal dashboard
- HOD dashboard
- Student management
- Teacher management
- Parent management
- Academic management
- Notice and event management
- Media and gallery management
- Alumni media and stories
- Downloads and publications
- Reports and analytics

## 17. Rollout Priority

### Phase 1

- Rename product direction to CMS
- Build shared red-led design system
- Build shared shell

### Phase 2

- Build new landing page
- Build public department listing
- Build alumni media page

### Phase 3

- Build principal dashboard
- Upgrade HOD/admin UI while keeping current admin familiarity

### Phase 4

- Extend department CMS features
- Improve notices, resources, events, and media flows

### Phase 5

- Refine teacher, student, and parent portals into the shared CMS design system

## 18. Non-Negotiables

- System name must be `CMS`
- CMS must support the whole college and multiple departments
- Principal dashboard must be supported
- HOD/admin UI must stay familiar to the current admin workflow
- Landing page must follow the new structure inspired by the provided screenshot
- A new alumni media page must be added
- Red must be the system-wide primary color identity
- Existing academic workflows must remain supported

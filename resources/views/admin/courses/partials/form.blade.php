@php
    $course = $course ?? new \App\Models\Course();
    $semesters = collect($semesters ?? []);
    $allTeachers = collect($allTeachers ?? []);
    $labTechnicians = collect($labTechnicians ?? []);
    $selectedTeacherId = old('teacher_id', $selectedTeacherId ?? $course->teacher_id ?? '');
    $selectedSemester = old('semester', $course->semester ?? '');
    $selectedSubjectType = old('subject_type', $course->subject_type ?? 'core');
    $selectedLabTechId = old('lab_technician_id', $course->lab_technician_id ?? '');
    $isEdit = $isEdit ?? false;
    $formAction = $formAction ?? route('admin.courses.store');
    $submitLabel = $submitLabel ?? 'Save Course';
    $existingLabDocument = $course->lab_document ?? null;
    $existingSyllabusDocument = $course->syllabus_document_path ?? null;
@endphp

<style>
    .course-form-shell,.course-side-card,.course-page-section{border:1px solid #e2e8f0;border-radius:1.25rem;background:linear-gradient(180deg,#fff 0%,#f8fafc 100%);box-shadow:0 18px 35px -30px rgba(15,23,42,.22)}
    .course-page-grid{display:grid;grid-template-columns:minmax(0,22rem) minmax(0,1fr);gap:1.5rem;align-items:stretch}
    .course-page-grid>div,.course-tab-panel,.course-form-shell,.course-side-card,.course-page-section{min-width:0}
    .course-page-grid>div:first-child{display:flex;flex-direction:column;gap:1.25rem;align-self:start}
    .course-side-card{padding:1.1rem}
    .course-summary{display:grid;gap:.75rem}
    .course-summary-box{padding:.9rem 1rem;border:1px solid #e2e8f0;border-radius:1rem;background:#fff}
    .course-label{display:block;margin-bottom:.42rem;font-size:.72rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#64748b}
    .course-value{font-weight:700;color:#0f172a}
    .course-tab-bar{display:flex;gap:.55rem;overflow-x:auto;padding:.45rem;border:1px solid #e2e8f0;border-radius:1rem;background:linear-gradient(180deg,#fff 0%,#f8fafc 100%)}
    .course-tab-btn{display:inline-flex;align-items:center;gap:.45rem;border:1px solid transparent;border-radius:.95rem;padding:.75rem 1rem;font-size:.9rem;font-weight:700;color:#475569;background:transparent;white-space:nowrap;transition:all .2s ease}
    .course-tab-btn:hover{border-color:#fb7185;color:#be123c;background:#fff1f2;transform:translateY(-1px)}
    .course-tab-btn.active{border-color:#fecdd3;background:#fff;color:#be123c;box-shadow:0 14px 28px -24px rgba(225,29,72,.45)}
    .course-tab-panel{display:none}
    .course-tab-panel.is-active{display:block}
    .course-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}
    .course-form-grid>div{min-width:0}
    .course-input,.course-select,.course-textarea{width:100%;min-height:2.95rem;border:2px solid #cbd5e1;border-radius:.9rem;background:#fff;padding:.75rem .95rem;transition:border-color .2s ease,box-shadow .2s ease}
    .course-textarea{min-height:5.75rem;resize:vertical}
    .course-input:focus,.course-select:focus,.course-textarea:focus{outline:none;border-color:#e11d48;box-shadow:0 0 0 4px rgba(225,29,72,.12)}
    .course-help{margin-top:.35rem;font-size:.75rem;color:#64748b}
    .course-error{margin-top:.35rem;min-height:1rem;font-size:.75rem;color:#dc2626}
    .course-btn-primary,.course-btn-secondary{display:inline-flex;align-items:center;justify-content:center;gap:.55rem;border-radius:.95rem;font-weight:700;transition:transform .2s ease,box-shadow .2s ease}
    .course-btn-primary:hover,.course-btn-secondary:hover{transform:translateY(-1px)}
    .course-btn-primary{padding:.82rem 1rem;background:linear-gradient(135deg,#16a34a 0%,#22c55e 100%);color:#fff;box-shadow:0 18px 34px -24px rgba(34,197,94,.55)}
    .course-btn-secondary{padding:.82rem 1rem;border:1px solid #cbd5e1;background:#fff;color:#334155}
    .course-form-actions{position:sticky;bottom:1rem;z-index:10;margin-top:1.5rem;border:1px solid #e2e8f0;border-radius:1rem;background:rgba(255,255,255,.92);backdrop-filter:blur(8px);box-shadow:0 18px 35px -30px rgba(15,23,42,.22)}
    .course-file-pill{display:inline-flex;align-items:center;gap:.45rem;padding:.55rem .82rem;border:1px solid #e2e8f0;border-radius:999px;background:#fff;font-size:.82rem;font-weight:600;color:#334155}
    .course-chip{display:inline-flex;align-items:center;justify-content:center;border-radius:999px;padding:.35rem .8rem;font-size:.75rem;font-weight:700}
    .course-chip.active{background:#dcfce7;color:#166534}
    .course-chip.inactive{background:#fee2e2;color:#b91c1c}
    .course-chip.soft{background:#eef2ff;color:#4338ca}
    @media (max-width:1024px){.course-page-grid{grid-template-columns:1fr}}
    @media (max-width:640px){.course-form-grid{grid-template-columns:1fr}.course-tab-btn{padding:.7rem .85rem;font-size:.85rem}}
</style>

<div class="course-form-shell overflow-hidden p-6">
    <div class="relative z-10 flex flex-col gap-5 xl:flex-row xl:items-start">
        <div class="w-full xl:max-w-[22rem]">
            <div class="course-side-card">
                <p class="course-label">Course Snapshot</p>
                <div class="mt-4 flex flex-col items-center gap-4 text-center">
                    <div class="flex h-28 w-28 items-center justify-center rounded-full bg-rose-100 text-5xl font-bold text-rose-500">
                        <i class="bi bi-book-half"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ old('subject_name', $course->subject_name ?? 'New Course') }}</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ old('subject_code', $course->subject_code ?? 'No code assigned') }}</p>
                    </div>
                </div>
                <div class="mt-4 grid gap-3">
                    <div class="course-summary-box"><label class="course-label">Semester</label><p class="course-value">{{ $selectedSemester ?: 'Not set' }}</p></div>
                    <div class="course-summary-box"><label class="course-label">Credits</label><p class="course-value">{{ old('credits', $course->credits ?? 3) }}</p></div>
                    <div class="course-summary-box"><label class="course-label">Status</label><p class="course-value">{{ ucfirst(old('status', $course->status ?? 'active')) }}</p></div>
                </div>
            </div>
        </div>

        <div class="w-full min-w-0 xl:flex-1">
            <div class="course-page-section p-5">
                <div class="mb-5">
                    <p class="course-label">Subject Management</p>
                    <h2 class="mt-1 text-2xl font-bold text-slate-900">{{ $isEdit ? 'Edit Course' : 'Create a new course' }}</h2>
                    <p class="mt-2 text-sm text-slate-500">Use the grouped tabs to keep the subject record clear and easy to review on smaller screens.</p>
                </div>

                <div class="course-tab-bar" role="tablist" aria-label="Course form sections">
                    <button type="button" class="course-tab-btn active" data-course-tab="basic"><i class="bi bi-info-circle"></i>Basic Info</button>
                    <button type="button" class="course-tab-btn" data-course-tab="assessment"><i class="bi bi-clipboard-check"></i>Assessment</button>
                    <button type="button" class="course-tab-btn" data-course-tab="lab"><i class="bi bi-beaker"></i>Lab / Elective</button>
                    <button type="button" class="course-tab-btn" data-course-tab="details"><i class="bi bi-journal-text"></i>Details</button>
                    <button type="button" class="course-tab-btn" data-course-tab="hours"><i class="bi bi-clock"></i>Hours</button>
                </div>

                <form id="courseForm" action="{{ $formAction }}" method="POST" enctype="multipart/form-data" class="mt-5">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif
                    <input type="hidden" name="id" value="{{ $course->id ?? '' }}">

                    <section data-course-tab-panel="basic" class="course-tab-panel is-active space-y-6">
                        <div class="course-page-section p-5">
                            <p class="course-label">Basic Information</p>
                            <div class="course-form-grid mt-4">
                                <div class="md:col-span-2">
                                    <label class="course-label" for="courseName">Course Name *</label>
                                    <input type="text" name="subject_name" id="courseName" value="{{ old('subject_name', $course->subject_name ?? '') }}" class="course-input @error('subject_name') border-red-500 @enderror" placeholder="e.g., Data Structures" required>
                                    <p class="course-error">@error('subject_name'){{ $message }}@enderror</p>
                                </div>
                                <div>
                                    <label class="course-label" for="courseCode">Course Code *</label>
                                    <input type="text" name="subject_code" id="courseCode" value="{{ old('subject_code', $course->subject_code ?? '') }}" class="course-input @error('subject_code') border-red-500 @enderror" placeholder="e.g., CS-301" required>
                                    <p class="course-error">@error('subject_code'){{ $message }}@enderror</p>
                                </div>
                                <div>
                                    <label class="course-label" for="courseSemester">Semester</label>
                                    <select name="semester" id="courseSemester" class="course-select @error('semester') border-red-500 @enderror">
                                        <option value="">All Semesters</option>
                                        @foreach($semesters as $sem)
                                            <option value="{{ $sem->number }}" @selected((string) $selectedSemester === (string) $sem->number)>
                                                {{ $sem->name ?? \App\Models\Semester::getOrdinalName((int) $sem->number) ?? 'Semester ' . $sem->number }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="course-error">@error('semester'){{ $message }}@enderror</p>
                                </div>
                                <div>
                                    <label class="course-label" for="courseCredits">Credits</label>
                                    <input type="number" name="credits" id="courseCredits" value="{{ old('credits', $course->credits ?? 3) }}" min="1" max="10" class="course-input @error('credits') border-red-500 @enderror">
                                    <p class="course-error">@error('credits'){{ $message }}@enderror</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="course-label" for="courseTeacher">Assign Teacher</label>
                                    <select name="teacher_id" id="courseTeacher" class="course-select @error('teacher_id') border-red-500 @enderror">
                                        <option value="">Unassigned</option>
                                        @foreach($allTeachers as $teacher)
                                            <option value="{{ $teacher->id }}" @selected((string) $selectedTeacherId === (string) $teacher->id)>{{ $teacher->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="course-error">@error('teacher_id'){{ $message }}@enderror</p>
                                </div>
                                <div>
                                    <label class="course-label" for="courseStatus">Status</label>
                                    <select name="status" id="courseStatus" class="course-select @error('status') border-red-500 @enderror">
                                        <option value="active" @selected(old('status', $course->status ?? 'active') === 'active')>Active</option>
                                        <option value="archived" @selected(old('status', $course->status ?? '') === 'archived')>Archived</option>
                                    </select>
                                    <p class="course-error">@error('status'){{ $message }}@enderror</p>
                                </div>
                                <div>
                                    <label class="course-label" for="courseType">Subject Type</label>
                                    <select name="subject_type" id="courseType" class="course-select @error('subject_type') border-red-500 @enderror">
                                        @foreach(['core' => 'Core Subject', 'elective' => 'Elective Subject', 'optional' => 'Optional Subject', 'major' => 'Major Subject', 'project' => 'Project Subject'] as $value => $label)
                                            <option value="{{ $value }}" @selected($selectedSubjectType === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <p class="course-error">@error('subject_type'){{ $message }}@enderror</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section data-course-tab-panel="assessment" class="course-tab-panel space-y-6">
                        <div class="course-page-section p-5">
                            <p class="course-label">Assessment Pattern</p>
                            <div class="course-form-grid mt-4">
                                <div>
                                    <label class="course-label" for="courseTheoryPct">Theory Percentage</label>
                                    <input type="number" name="theory_percentage" id="courseTheoryPct" value="{{ old('theory_percentage', $course->theory_percentage ?? 70) }}" min="0" max="100" class="course-input">
                                </div>
                                <div>
                                    <label class="course-label" for="coursePracticalPct">Practical Percentage</label>
                                    <input type="number" name="practical_percentage" id="coursePracticalPct" value="{{ old('practical_percentage', $course->practical_percentage ?? 30) }}" min="0" max="100" class="course-input">
                                </div>
                                <div>
                                    <label class="course-label" for="courseInternalPct">Internal Percentage</label>
                                    <input type="number" name="internal_percentage" id="courseInternalPct" value="{{ old('internal_percentage', $course->internal_percentage ?? 40) }}" min="0" max="100" class="course-input">
                                </div>
                                <div>
                                    <label class="course-label" for="courseExternalPct">External Percentage</label>
                                    <input type="number" name="external_percentage" id="courseExternalPct" value="{{ old('external_percentage', $course->external_percentage ?? 60) }}" min="0" max="100" class="course-input">
                                </div>
                            </div>
                        </div>
                    </section>

                    <section data-course-tab-panel="lab" class="course-tab-panel space-y-6">
                        <div class="course-page-section p-5">
                            <p class="course-label">Lab & Elective Settings</p>
                            <div class="mt-4 space-y-4">
                                <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                                    <input type="checkbox" name="has_lab" id="courseHasLab" value="1" class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500" @checked(old('has_lab', $course->has_lab ?? false))>
                                    <span>Has Lab</span>
                                </label>
                                <div class="course-form-grid">
                                    <div>
                                        <label class="course-label" for="courseLabTech">Lab Technician</label>
                                        <select name="lab_technician_id" id="courseLabTech" class="course-select">
                                            <option value="">Not Assigned</option>
                                            @foreach($labTechnicians as $technician)
                                                <option value="{{ $technician->id }}" @selected((string) $selectedLabTechId === (string) $technician->id)>{{ $technician->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="course-label" for="courseElectiveGroup">Elective Group</label>
                                        <select name="elective_group" id="courseElectiveGroup" class="course-select">
                                            <option value="">Select group</option>
                                            @foreach(['I','II','III','IV','V'] as $group)
                                                <option value="{{ $group }}" @selected(old('elective_group', $course->elective_group ?? '') === $group)>{{ $group }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="course-label" for="courseMaxStudents">Max Students</label>
                                        <input type="number" name="max_students" id="courseMaxStudents" value="{{ old('max_students', $course->max_students ?? '') }}" min="1" class="course-input">
                                    </div>
                                    <div>
                                        <label class="course-label" for="courseMinStudents">Min Students</label>
                                        <input type="number" name="min_students" id="courseMinStudents" value="{{ old('min_students', $course->min_students ?? '') }}" min="1" class="course-input">
                                    </div>
                                </div>
                                <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                                    <input type="checkbox" name="is_elective_open" id="courseElectiveOpen" value="1" class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500" @checked(old('is_elective_open', $course->is_elective_open ?? false))>
                                    <span>Elective Enrollment Open</span>
                                </label>
                                <div class="course-form-grid">
                                    <div>
                                        <label class="course-label" for="courseLabDocument">Lab Document</label>
                                        <input type="file" name="lab_document" id="courseLabDocument" class="course-input p-2 text-sm">
                                        @if($existingLabDocument)
                                            <p class="course-help">Existing: {{ basename($existingLabDocument) }}</p>
                                        @endif
                                        <p class="course-error">@error('lab_document'){{ $message }}@enderror</p>
                                    </div>
                                    <div>
                                        <label class="course-label" for="courseSyllabusDocument">Syllabus Document</label>
                                        <input type="file" name="syllabus_document" id="courseSyllabusDocument" class="course-input p-2 text-sm">
                                        @if($existingSyllabusDocument)
                                            <p class="course-help">Existing: {{ basename($existingSyllabusDocument) }}</p>
                                        @endif
                                        <p class="course-error">@error('syllabus_document'){{ $message }}@enderror</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section data-course-tab-panel="details" class="course-tab-panel space-y-6">
                        <div class="course-page-section p-5">
                            <p class="course-label">Additional Details</p>
                            <div class="course-form-grid mt-4">
                                <div class="md:col-span-2">
                                    <label class="course-label" for="courseDescription">Description</label>
                                    <textarea name="description" id="courseDescription" rows="3" class="course-textarea @error('description') border-red-500 @enderror" placeholder="Add a short description...">{{ old('description', $course->description ?? '') }}</textarea>
                                    <p class="course-error">@error('description'){{ $message }}@enderror</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="course-label" for="courseDescriptionNe">Description (Nepali)</label>
                                    <textarea name="description_ne" id="courseDescriptionNe" rows="3" class="course-textarea @error('description_ne') border-red-500 @enderror" placeholder="नेपालीमा विवरण">{{ old('description_ne', $course->description_ne ?? '') }}</textarea>
                                    <p class="course-error">@error('description_ne'){{ $message }}@enderror</p>
                                </div>
                                <div>
                                    <label class="course-label" for="coursePrerequisite">Prerequisite</label>
                                    <input type="text" name="prerequisite" id="coursePrerequisite" value="{{ old('prerequisite', $course->prerequisite ?? '') }}" class="course-input @error('prerequisite') border-red-500 @enderror" placeholder="e.g., Basic programming">
                                    <p class="course-error">@error('prerequisite'){{ $message }}@enderror</p>
                                </div>
                                <div>
                                    <label class="course-label" for="courseRemarks">Remarks</label>
                                    <textarea name="remarks" id="courseRemarks" rows="3" class="course-textarea @error('remarks') border-red-500 @enderror" placeholder="Internal notes (optional)">{{ old('remarks', $course->remarks ?? '') }}</textarea>
                                    <p class="course-error">@error('remarks'){{ $message }}@enderror</p>
                                </div>
                                <div>
                                    <label class="course-label" for="courseStartDate">Start Date</label>
                                    <input type="date" name="start_date" id="courseStartDate" value="{{ old('start_date', !empty($course->start_date) ? \Illuminate\Support\Carbon::parse($course->start_date)->format('Y-m-d') : '') }}" class="course-input">
                                </div>
                                <div>
                                    <label class="course-label" for="courseEndDate">End Date</label>
                                    <input type="date" name="end_date" id="courseEndDate" value="{{ old('end_date', !empty($course->end_date) ? \Illuminate\Support\Carbon::parse($course->end_date)->format('Y-m-d') : '') }}" class="course-input">
                                </div>
                            </div>
                        </div>
                    </section>

                    <section data-course-tab-panel="hours" class="course-tab-panel space-y-6">
                        <div class="course-page-section p-5">
                            <p class="course-label">Teaching Hours</p>
                            <div class="course-form-grid mt-4">
                                <div>
                                    <label class="course-label" for="courseLectureHours">Lecture Hours</label>
                                    <input type="number" name="lecture_hours" id="courseLectureHours" value="{{ old('lecture_hours', $course->lecture_hours ?? 4) }}" min="0" max="10" class="course-input">
                                </div>
                                <div>
                                    <label class="course-label" for="coursePracticalHours">Practical Hours</label>
                                    <input type="number" name="practical_hours" id="coursePracticalHours" value="{{ old('practical_hours', $course->practical_hours ?? 2) }}" min="0" max="10" class="course-input">
                                </div>
                                <div>
                                    <label class="course-label" for="courseTutorialHours">Tutorial Hours</label>
                                    <input type="number" name="tutorial_hours" id="courseTutorialHours" value="{{ old('tutorial_hours', $course->tutorial_hours ?? 1) }}" min="0" max="10" class="course-input">
                                </div>
                                <div>
                                    <label class="course-label" for="coursePracticalFullMarks">Practical Full Marks</label>
                                    <input type="number" name="practical_full_marks" id="coursePracticalFullMarks" value="{{ old('practical_full_marks', $course->practical_full_marks ?? '') }}" min="0" class="course-input">
                                </div>
                                <div>
                                    <label class="course-label" for="coursePracticalPassMarks">Practical Pass Marks</label>
                                    <input type="number" name="practical_pass_marks" id="coursePracticalPassMarks" value="{{ old('practical_pass_marks', $course->practical_pass_marks ?? '') }}" min="0" class="course-input">
                                </div>
                                <div>
                                    <label class="course-label" for="coursePracticalObtainedMarks">Practical Obtained Marks</label>
                                    <input type="number" name="practical_obtained_marks" id="coursePracticalObtainedMarks" value="{{ old('practical_obtained_marks', $course->practical_obtained_marks ?? '') }}" min="0" class="course-input">
                                </div>
                            </div>
                        </div>

                        <div class="course-form-actions p-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <button type="button" id="coursePrevBtn" class="course-btn-secondary w-full sm:w-auto"><i class="bi bi-arrow-left"></i>Previous</button>
                                <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                                    <button type="button" id="courseNextBtn" class="course-btn-primary w-full sm:w-auto"><i class="bi bi-arrow-right"></i>Next</button>
                                    <button type="submit" id="courseSaveBtn" class="course-btn-primary hidden w-full sm:w-auto"><i class="bi bi-check2-circle"></i>{{ $submitLabel }}</button>
                                </div>
                            </div>
                        </div>
                    </section>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const tabButtons = Array.from(document.querySelectorAll('[data-course-tab]'));
        const tabPanels = Array.from(document.querySelectorAll('[data-course-tab-panel]'));
        const prevBtn = document.getElementById('coursePrevBtn');
        const nextBtn = document.getElementById('courseNextBtn');
        const saveBtn = document.getElementById('courseSaveBtn');
        const tabOrder = tabButtons.map(button => button.dataset.courseTab);

        function openTab(tabName) {
            tabButtons.forEach(button => {
                const active = button.dataset.courseTab === tabName;
                button.classList.toggle('active', active);
                button.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            tabPanels.forEach(panel => {
                panel.classList.toggle('is-active', panel.dataset.courseTabPanel === tabName);
            });

            const currentIndex = tabOrder.indexOf(tabName);
            if (prevBtn) {
                prevBtn.disabled = currentIndex <= 0;
                prevBtn.classList.toggle('opacity-50', currentIndex <= 0);
                prevBtn.classList.toggle('cursor-not-allowed', currentIndex <= 0);
            }
            if (nextBtn && saveBtn) {
                const isLast = currentIndex === tabOrder.length - 1;
                nextBtn.classList.toggle('hidden', isLast);
                saveBtn.classList.toggle('hidden', !isLast);
            }
        }

        tabButtons.forEach((button, index) => button.addEventListener('click', () => openTab(tabOrder[index])));
        prevBtn?.addEventListener('click', () => {
            const activeIndex = tabOrder.findIndex(name => document.querySelector(`[data-course-tab="${name}"]`)?.classList.contains('active'));
            if (activeIndex > 0) openTab(tabOrder[activeIndex - 1]);
        });
        nextBtn?.addEventListener('click', () => {
            const activeIndex = tabOrder.findIndex(name => document.querySelector(`[data-course-tab="${name}"]`)?.classList.contains('active'));
            if (activeIndex >= 0 && activeIndex < tabOrder.length - 1) openTab(tabOrder[activeIndex + 1]);
        });

        openTab(tabOrder[0] || 'basic');
    })();
</script>


@php
    $exam = $exam ?? null;
    $isEdit = $isEdit ?? false;
    $formAction = $formAction ?? route('admin.exam.store');
    $backRoute = $backRoute ?? route('admin.exam');
    $submitLabel = $submitLabel ?? ($isEdit ? 'Update Exam' : 'Create Exam');
    $semesterOptions = $activeSemesters ?? $semesters ?? [];
    $selectedSemester = old('semester', $exam?->semester ?? '');
    $selectedSubject = old('subject_id', $exam?->subject_id ?? '');
    $selectedCategory = old('exam_category', $exam?->exam_category ?? 'assessment');
    $selectedStatus = old('status', $exam?->status ?? 'draft');
    $selectedAcademicYear = old('academic_year', $exam?->academic_year ?? '');
    $selectedAssessmentNumber = old('assessment_number', $exam?->assessment_number ?? '');
    $selectedExamName = old('exam_name', $exam?->exam_name ?? '');
    $selectedExamNameNe = old('exam_name_ne', $exam?->exam_name_ne ?? '');
    $selectedExamDate = old('exam_date', $exam?->exam_date?->format('Y-m-d') ?? '');
    $selectedExamDateBs = old('exam_date_bs', $exam?->exam_date_bs ?? '');
    $selectedDescription = old('description', $exam?->description ?? '');
    $selectedInstructions = old('instructions', $exam?->instructions ?? '');
    $selectedFullMarks = old('full_marks', $exam?->full_marks ?? 0);
    $selectedPassingMarks = old('passing_marks', $exam?->passing_marks ?? 0);
    $subjects = $subjects ?? collect();

    $examComponentDefinitions = [
        'assessment' => [
            ['key' => 'theory', 'label' => 'Theory'],
            ['key' => 'practical', 'label' => 'Practical'],
            ['key' => 'viva', 'label' => 'Viva'],
        ],
        'ctevt' => [
            ['key' => 'theory_internal', 'label' => 'Theory Internal'],
            ['key' => 'theory_external', 'label' => 'Theory External'],
            ['key' => 'practical_internal', 'label' => 'Practical Internal'],
            ['key' => 'practical_external', 'label' => 'Practical External'],
        ],
    ];

    $examComponentFields = [
        'ctevt' => [
            'theory_internal' => ['max' => 'theory_internal_max_marks', 'pass' => 'theory_internal_pass_marks'],
            'theory_external' => ['max' => 'theory_external_max_marks', 'pass' => 'theory_external_pass_marks'],
            'practical_internal' => ['max' => 'practical_internal_max_marks', 'pass' => 'practical_internal_pass_marks'],
            'practical_external' => ['max' => 'practical_external_max_marks', 'pass' => 'practical_external_pass_marks'],
        ],
    ];
@endphp

<div class="grid grid-cols-1 xl:grid-cols-[280px_minmax(0,1fr)] gap-6">
    <aside class="space-y-4">
        <div class="rounded-2xl border border-rose-100 bg-white shadow-sm p-5">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-slate-400">Exam Setup</p>
            <h2 class="mt-2 text-xl font-bold text-slate-900">{{ $isEdit ? 'Edit Exam' : 'Create Exam' }}</h2>
            <p class="mt-2 text-sm text-slate-500">
                Configure the semester, subject, category, and marks structure from one page.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4 space-y-3">
            <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-slate-400">Mode</p>
                <p class="mt-1 text-sm font-semibold text-slate-900">{{ $isEdit ? 'Update existing exam' : 'New exam record' }}</p>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-slate-400">Category</p>
                <p class="mt-1 text-sm font-semibold text-slate-900" id="examCategorySummary">{{ ucfirst($selectedCategory) }}</p>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-slate-400">Semester</p>
                <p class="mt-1 text-sm font-semibold text-slate-900" id="examSemesterSummary">{{ $selectedSemester ?: 'Not selected' }}</p>
            </div>
        </div>
    </aside>

    <form id="examForm" method="POST" action="{{ $formAction }}" data-mode="{{ $isEdit ? 'edit' : 'create' }}" data-selected-subject="{{ $selectedSubject }}" class="min-w-0">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <input type="hidden" name="full_marks" id="examFullMarks" value="{{ $selectedFullMarks }}">
        <input type="hidden" name="passing_marks" id="examPassingMarks" value="{{ $selectedPassingMarks }}">

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-semibold text-slate-900">Academic Information</h3>
                    <p class="text-xs text-slate-500 mt-1">Choose semester and subject first so the exam stays linked correctly.</p>
                </div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500 mb-1.5">Semester *</label>
                        <select id="examSemester" name="semester" class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:border-rose-500 focus:ring-rose-500">
                            <option value="">Select semester</option>
                            <option value="all" @selected($selectedSemester === 'all')>All Semesters</option>
                            @foreach($semesterOptions as $key => $label)
                                <option value="{{ $key }}" @selected((string) $selectedSemester === (string) $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500 mb-1.5">Subject *</label>
                        <select id="examSubject" name="subject_id" data-selected-subject="{{ $selectedSubject }}" class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:border-rose-500 focus:ring-rose-500">
                            <option value="">Select subject</option>
                            <option value="all" @selected((string) $selectedSubject === 'all')>All Subjects</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected((string) $selectedSubject === (string) $subject->id)>
                                    {{ $subject->subject_name }}{{ $subject->subject_code ? ' - '.$subject->subject_code : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500 mb-1.5">Academic Year</label>
                        <input type="text" name="academic_year" value="{{ $selectedAcademicYear }}" placeholder="2080-2081" class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:border-rose-500 focus:ring-rose-500">
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-semibold text-slate-900">Exam Details</h3>
                    <p class="text-xs text-slate-500 mt-1">Core exam identity, category, and schedule fields.</p>
                </div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500 mb-1.5">Exam Category *</label>
                        <select id="examCategory" name="exam_category" class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:border-rose-500 focus:ring-rose-500">
                            <option value="assessment" @selected($selectedCategory === 'assessment')>Assessment</option>
                            <option value="ctevt" @selected($selectedCategory === 'ctevt')>CTEVT</option>
                            <option value="general" @selected($selectedCategory === 'general')>General</option>
                        </select>
                    </div>

                    <div id="assessmentNumberWrap">
                        <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500 mb-1.5">Assessment Number</label>
                        <input type="number" name="assessment_number" value="{{ $selectedAssessmentNumber }}" min="1" placeholder="Auto-generated if blank" class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:border-rose-500 focus:ring-rose-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500 mb-1.5">Exam Name *</label>
                        <input type="text" name="exam_name" value="{{ $selectedExamName }}" placeholder="Enter exam name" class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:border-rose-500 focus:ring-rose-500" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500 mb-1.5">Exam Name (Nepali)</label>
                        <input type="text" name="exam_name_ne" value="{{ $selectedExamNameNe }}" placeholder="नेपालीमा परीक्षा नाम" class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:border-rose-500 focus:ring-rose-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500 mb-1.5">Exam Date (AD) *</label>
                        <input type="date" id="examDateAd" name="exam_date" value="{{ $selectedExamDate }}" class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:border-rose-500 focus:ring-rose-500" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500 mb-1.5">Exam Date (BS)</label>
                        <input type="text" id="examDateBs" name="exam_date_bs" value="{{ $selectedExamDateBs }}" placeholder="YYYY-MM-DD" class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:border-rose-500 focus:ring-rose-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500 mb-1.5">Status *</label>
                        <select name="status" class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:border-rose-500 focus:ring-rose-500" required>
                            <option value="draft" @selected($selectedStatus === 'draft')>Draft</option>
                            <option value="published" @selected($selectedStatus === 'published')>Published</option>
                            <option value="archived" @selected($selectedStatus === 'archived')>Archived</option>
                            <option value="faculty" @selected($selectedStatus === 'faculty')>Faculty</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-semibold text-slate-900">Marks Configuration</h3>
                    <p class="text-xs text-slate-500 mt-1">Assessment uses total/pass marks. CTEVT uses component totals.</p>
                </div>
                <div class="p-5 space-y-4">
                    <div id="assessmentMarksSection" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500 mb-1.5">Total Marks *</label>
                            <input type="number" id="assessmentFullMarks" value="{{ $selectedFullMarks }}" min="0" step="1" placeholder="100" class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:border-rose-500 focus:ring-rose-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500 mb-1.5">Passing Marks *</label>
                            <input type="number" id="assessmentPassingMarks" value="{{ $selectedPassingMarks }}" min="0" step="1" placeholder="40" class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:border-rose-500 focus:ring-rose-500">
                        </div>
                    </div>

                    <div id="ctevtComponentsSection" class="space-y-4 hidden">
                        <p class="text-sm text-slate-500">Set component marks for CTEVT exams. The total fields update automatically.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($examComponentDefinitions['ctevt'] as $component)
                                @php $fields = $examComponentFields['ctevt'][$component['key']] ?? ['max' => '', 'pass' => '']; @endphp
                                <div class="rounded-xl border border-dashed border-slate-200 p-4 space-y-3">
                                    <h4 class="text-sm font-semibold text-slate-900">{{ $component['label'] }}</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-[11px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-1.5">Full Marks</label>
                                            <input type="number" min="0" step="0.5" name="{{ $fields['max'] }}" data-component-group="ctevt" data-value-type="max" class="component-input w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:border-rose-500 focus:ring-rose-500" placeholder="0">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-1.5">Pass Marks</label>
                                            <input type="number" min="0" step="0.5" name="{{ $fields['pass'] }}" data-component-group="ctevt" data-value-type="pass" class="component-input w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:border-rose-500 focus:ring-rose-500" placeholder="0">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-semibold text-slate-900">Additional Information</h3>
                    <p class="text-xs text-slate-500 mt-1">Notes, instructions, and guidance for staff.</p>
                </div>
                <div class="p-5 grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500 mb-1.5">Description</label>
                        <textarea name="description" rows="4" placeholder="Enter exam description..." class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:border-rose-500 focus:ring-rose-500">{{ $selectedDescription }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500 mb-1.5">Instructions</label>
                        <textarea name="instructions" rows="3" placeholder="Enter exam instructions..." class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:border-rose-500 focus:ring-rose-500">{{ $selectedInstructions }}</textarea>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">
                    Review the form before saving. Assessment exams can auto-generate the next assessment number.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                    <a href="{{ $backRoute }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-rose-700 transition shadow-sm">
                        {{ $submitLabel }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>


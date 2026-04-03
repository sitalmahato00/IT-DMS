<script>
    document.addEventListener('DOMContentLoaded', () => {
        const semesterSelect = document.getElementById('examSemester');
        const subjectSelect = document.getElementById('examSubject');
        const categorySelect = document.getElementById('examCategory');
        const assessmentSection = document.getElementById('assessmentMarksSection');
        const ctevtSection = document.getElementById('ctevtComponentsSection');
        const assessmentNumberWrap = document.getElementById('assessmentNumberWrap');
        const assessmentFull = document.getElementById('assessmentFullMarks');
        const assessmentPass = document.getElementById('assessmentPassingMarks');
        const hiddenFull = document.getElementById('examFullMarks');
        const hiddenPass = document.getElementById('examPassingMarks');
        const dateAd = document.getElementById('examDateAd');
        const dateBs = document.getElementById('examDateBs');
        const form = document.getElementById('examForm');
        const submitBtn = document.querySelector('#examForm button[type="submit"]');
        const subjectsUrl = @json(route('admin.exam.subjects'));
        const convertUrl = @json(route('admin.exam.convert-date'));
        const selectedSubject = form?.dataset.selectedSubject || '';
        const isEdit = form?.dataset.mode === 'edit';

        function setCategorySummary() {
            const summary = document.getElementById('examCategorySummary');
            if (summary && categorySelect) {
                summary.textContent = categorySelect.value ? categorySelect.value.charAt(0).toUpperCase() + categorySelect.value.slice(1) : 'Not selected';
            }
        }

        function setSemesterSummary() {
            const summary = document.getElementById('examSemesterSummary');
            if (summary && semesterSelect) {
                summary.textContent = semesterSelect.value || 'Not selected';
            }
        }

        function syncAssessmentTotals() {
            if (!assessmentFull || !assessmentPass || !hiddenFull || !hiddenPass) return;
            hiddenFull.value = assessmentFull.value || 0;
            hiddenPass.value = assessmentPass.value || 0;
        }

        function syncCtevtTotals() {
            if (!hiddenFull || !hiddenPass) return;
            let fullSum = 0;
            let passSum = 0;
            document.querySelectorAll('.component-input[data-value-type="max"]').forEach((input) => {
                fullSum += parseFloat(input.value || '0') || 0;
            });
            document.querySelectorAll('.component-input[data-value-type="pass"]').forEach((input) => {
                passSum += parseFloat(input.value || '0') || 0;
            });
            hiddenFull.value = fullSum || 0;
            hiddenPass.value = passSum || 0;
        }

        function updateSections() {
            const category = categorySelect?.value || 'assessment';
            const isAssessment = category === 'assessment';

            if (assessmentSection) assessmentSection.classList.toggle('hidden', !isAssessment);
            if (ctevtSection) ctevtSection.classList.toggle('hidden', isAssessment);
            if (assessmentNumberWrap) assessmentNumberWrap.classList.toggle('hidden', !isAssessment);

            setCategorySummary();

            if (isAssessment) {
                syncAssessmentTotals();
            } else {
                syncCtevtTotals();
            }
        }

        async function convertDate(direction, sourceInput, targetInput) {
            const source = sourceInput?.value || '';
            if (!source || !targetInput) return;

            try {
                const res = await fetch(convertUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    },
                    body: JSON.stringify({ date: source, from: direction.from, to: direction.to }),
                });

                if (!res.ok) return;
                const data = await res.json();
                targetInput.value = data.converted_date || '';
            } catch (error) {
                console.error('Date conversion failed:', error);
            }
        }

        async function loadSubjects(keepSelected = true) {
            if (!semesterSelect || !subjectSelect) return;

            const semester = semesterSelect.value || '';
            const previouslySelected = keepSelected ? (subjectSelect.value || selectedSubject) : selectedSubject;

            if (!semester) {
                setSemesterSummary();
                return;
            }

            subjectSelect.disabled = true;
            subjectSelect.innerHTML = '<option value="">Loading subjects...</option>';

            try {
                const res = await fetch(`${subjectsUrl}?semester=${encodeURIComponent(semester)}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();

                const options = ['<option value="">Select subject</option>', '<option value="all">All Subjects</option>'];
                const subjects = [];

                if (data.grouped) {
                    Object.values(data.grouped).forEach((groupItems) => {
                        groupItems.forEach((subject) => subjects.push(subject));
                    });
                } else if (Array.isArray(data.subjects)) {
                    subjects.push(...data.subjects);
                }

                subjects.forEach((subject) => {
                    const label = `${subject.subject_name}${subject.subject_code ? ' - ' + subject.subject_code : ''}`;
                    options.push(`<option value="${subject.id}">${label}</option>`);
                });

                subjectSelect.innerHTML = options.join('');
                subjectSelect.disabled = false;

                if (previouslySelected && Array.from(subjectSelect.options).some((option) => option.value === String(previouslySelected))) {
                    subjectSelect.value = String(previouslySelected);
                }
            } catch (error) {
                console.error('Failed to load subjects:', error);
                subjectSelect.innerHTML = '<option value="">Select subject</option><option value="all">All Subjects</option>';
                subjectSelect.disabled = false;
            }

            setSemesterSummary();
        }

        categorySelect?.addEventListener('change', updateSections);
        semesterSelect?.addEventListener('change', async () => {
            await loadSubjects(true);
            updateSections();
        });
        subjectSelect?.addEventListener('change', updateSections);
        assessmentFull?.addEventListener('input', syncAssessmentTotals);
        assessmentPass?.addEventListener('input', syncAssessmentTotals);
        document.addEventListener('input', (event) => {
            if (event.target.matches('.component-input')) {
                syncCtevtTotals();
            }
        });

        dateAd?.addEventListener('change', () => convertDate({ from: 'ad', to: 'bs' }, dateAd, dateBs));
        dateBs?.addEventListener('change', () => convertDate({ from: 'bs', to: 'ad' }, dateBs, dateAd));

        if (submitBtn && document.getElementById('examForm')) {
            document.getElementById('examForm').addEventListener('submit', () => {
                submitBtn.disabled = true;
                submitBtn.textContent = isEdit ? 'Updating...' : 'Creating...';
            });
        }

        updateSections();
        setSemesterSummary();

        if (semesterSelect?.value) {
            loadSubjects(true).then(() => {
                if (selectedSubject) {
                    subjectSelect.value = String(selectedSubject);
                }
            });
        } else if (selectedSubject) {
            subjectSelect.value = String(selectedSubject);
        }
    });
</script>

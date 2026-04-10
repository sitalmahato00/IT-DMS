<div id="examsStatsInner" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
    <x-stats-card title="Total Exams" :value="$stats['total_exams'] ?? 0" icon="bi-clipboard-check" color="blue" />
    <x-stats-card title="Published" :value="$stats['published_exams'] ?? 0" icon="bi-check-circle" color="green" />
    <x-stats-card title="Draft" :value="$stats['draft_exams'] ?? 0" icon="bi-exclamation-circle" color="yellow" />
    <x-stats-card title="Total Marks Entries" :value="$stats['total_marks_entries'] ?? 0" icon="bi-question-circle" color="purple" />
</div>

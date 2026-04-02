<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const attendanceChartData = @json($attendanceChartData);
        const gradeDistribution = @json($gradeDistribution);
        const attendanceDataUrl = @json(route('admin.dashboard.attendance'));
        const totalGradedRecords = @json($totalGradedRecords);
        const gradePassRate = @json($passRate);
        const attendanceTarget = @json($attendanceTarget);
        const classStatusData = @json($classStatusData);
        const activityOverviewData = @json($activityOverviewData);
        const todayAttendanceRate = @json($todayAttendanceRate);
        const trackedTodayClasses = @json($dashboardOverview['today_class_count'] ?? 0);

        const isDark = document.documentElement.classList.contains('dark');
        const labelColor = isDark ? '#cbd5e1' : '#475569';
        const gridColor = isDark ? 'rgba(148, 163, 184, 0.18)' : 'rgba(148, 163, 184, 0.2)';
        const titleColor = isDark ? '#f8fafc' : '#0f172a';
        const mutedColor = isDark ? '#94a3b8' : '#64748b';
        const periodTitles = {
            week: 'Weekly classroom attendance snapshot',
            month: 'Monthly attendance pattern and consistency',
            semester: 'Semester-wide attendance trend'
        };

        const attendanceNodes = {
            caption: document.getElementById('attendanceTrendCaption'),
            average: document.getElementById('attendanceCurrentAverage'),
            range: document.getElementById('attendanceCurrentRange'),
            bestLabel: document.getElementById('attendanceBestLabel'),
            bestValue: document.getElementById('attendanceBestValue'),
            lowestLabel: document.getElementById('attendanceLowestLabel'),
            lowestValue: document.getElementById('attendanceLowestValue'),
            coverageValue: document.getElementById('attendanceCoverageValue'),
            coverageNote: document.getElementById('attendanceCoverageNote'),
        };

        const formatPercent = (value) => `${Number(value || 0).toFixed(1)}%`;
        const formatCount = (value) => Number(value || 0).toLocaleString();

        const updateAttendanceInsights = (chartData, period = 'week') => {
            const labels = chartData.labels || [];
            const details = (chartData.details || []).map((detail, index) => ({
                label: labels[index] || detail.period || 'N/A',
                present: Number(detail.present || 0),
                total: Number(detail.total || 0),
                percentage: Number(detail.percentage || 0),
            }));

            const active = details.filter((detail) => detail.total > 0);
            const totalTracked = active.reduce((sum, item) => sum + item.total, 0);
            const totalPresent = active.reduce((sum, item) => sum + item.present, 0);
            const average = active.length ? active.reduce((sum, item) => sum + item.percentage, 0) / active.length : 0;
            const best = active.reduce((winner, item) => (!winner || item.percentage > winner.percentage ? item : winner), null);
            const lowest = active.reduce((winner, item) => (!winner || item.percentage < winner.percentage ? item : winner), null);

            if (attendanceNodes.caption) attendanceNodes.caption.textContent = periodTitles[period] || periodTitles.week;
            if (attendanceNodes.average) attendanceNodes.average.textContent = formatPercent(average);
            if (attendanceNodes.range) attendanceNodes.range.textContent = active.length ? `${active.length} tracked periods` : 'No tracked periods';
            if (attendanceNodes.bestLabel) attendanceNodes.bestLabel.textContent = best ? best.label : 'No records';
            if (attendanceNodes.bestValue) attendanceNodes.bestValue.textContent = best ? `${formatPercent(best.percentage)} attendance` : 'Waiting for data';
            if (attendanceNodes.lowestLabel) attendanceNodes.lowestLabel.textContent = lowest ? lowest.label : 'No records';
            if (attendanceNodes.lowestValue) attendanceNodes.lowestValue.textContent = lowest ? `${formatPercent(lowest.percentage)} attendance` : 'Waiting for data';
            if (attendanceNodes.coverageValue) attendanceNodes.coverageValue.textContent = `${formatCount(totalPresent)} / ${formatCount(totalTracked)}`;
            if (attendanceNodes.coverageNote) {
                const missing = Math.max(totalTracked - totalPresent, 0);
                attendanceNodes.coverageNote.textContent = totalTracked ? `${formatCount(missing)} not present in this range` : 'No attendance records captured';
            }
        };

        const gradeCenterTextPlugin = {
            id: 'gradeCenterTextPlugin',
            afterDatasetsDraw(chart) {
                if (chart.canvas.id !== 'gradeDonutChart') return;
                const arc = chart.getDatasetMeta(0)?.data?.[0];
                if (!arc) return;
                const { ctx } = chart;
                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = titleColor;
                ctx.font = '700 24px system-ui';
                ctx.fillText(totalGradedRecords.toLocaleString(), arc.x, arc.y - 10);
                ctx.fillStyle = mutedColor;
                ctx.font = '600 11px system-ui';
                ctx.fillText('graded records', arc.x, arc.y + 10);
                ctx.fillText(`Pass ${formatPercent(gradePassRate)}`, arc.x, arc.y + 26);
                ctx.restore();
            }
        };

        const classCenterTextPlugin = {
            id: 'classCenterTextPlugin',
            afterDatasetsDraw(chart) {
                if (chart.canvas.id !== 'classStatusChart') return;
                const arc = chart.getDatasetMeta(0)?.data?.[0];
                if (!arc) return;
                const { ctx } = chart;
                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = titleColor;
                ctx.font = '700 22px system-ui';
                ctx.fillText(formatPercent(todayAttendanceRate), arc.x, arc.y - 8);
                ctx.fillStyle = mutedColor;
                ctx.font = '600 11px system-ui';
                ctx.fillText('engagement', arc.x, arc.y + 10);
                ctx.fillText(`${trackedTodayClasses} classes`, arc.x, arc.y + 24);
                ctx.restore();
            }
        };

        const gradeCanvas = document.getElementById('gradeDonutChart');
        const gradeNoData = document.getElementById('gradeDonutNoData');
        if (gradeCanvas) {
            const gradeLabels = Object.keys(gradeDistribution);
            const gradeValues = gradeLabels.map((label) => Number(gradeDistribution[label] || 0));
            const gradeColors = {
                'A+': '#22c55e',
                'A': '#16a34a',
                'B+': '#3b82f6',
                'B': '#2563eb',
                'C+': '#eab308',
                'C': '#ca8a04',
                'D': '#f97316',
                'F': '#ef4444'
            };
            const hasGradeData = gradeValues.some((value) => value > 0);

            if (!hasGradeData) {
                gradeCanvas.classList.add('hidden');
                if (gradeNoData) gradeNoData.classList.remove('hidden');
            } else {
                new Chart(gradeCanvas.getContext('2d'), {
                    type: 'doughnut',
                    plugins: [gradeCenterTextPlugin],
                    data: {
                        labels: gradeLabels,
                        datasets: [{
                            data: gradeValues,
                            backgroundColor: gradeLabels.map((label) => gradeColors[label] || '#94a3b8'),
                            borderWidth: 0,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label(context) {
                                        const value = Number(context.parsed || 0);
                                        const share = totalGradedRecords > 0 ? (value / totalGradedRecords) * 100 : 0;
                                        return `${context.label}: ${value} students (${share.toFixed(1)}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        const classStatusCanvas = document.getElementById('classStatusChart');
        const classStatusNoData = document.getElementById('classStatusNoData');
        if (classStatusCanvas) {
            const values = classStatusData.map((item) => Number(item.value || 0));
            const hasClassData = values.some((value) => value > 0);

            if (!hasClassData) {
                classStatusCanvas.classList.add('hidden');
                if (classStatusNoData) classStatusNoData.classList.remove('hidden');
            } else {
                new Chart(classStatusCanvas.getContext('2d'), {
                    type: 'doughnut',
                    plugins: [classCenterTextPlugin],
                    data: {
                        labels: classStatusData.map((item) => item.label),
                        datasets: [{
                            data: values,
                            backgroundColor: classStatusData.map((item) => item.color),
                            borderWidth: 0,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label(context) {
                                        return `${context.label}: ${Number(context.parsed || 0).toLocaleString()}`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
        const attendanceCanvas = document.getElementById('attendanceTrendChart');
        const attendanceNoData = document.getElementById('attendanceTrendNoData');
        const attendancePeriodSelect = document.getElementById('attendancePeriod');
        let attendanceChartInstance = null;

        const renderAttendanceTrendChart = (chartData, period = 'week') => {
            if (!attendanceCanvas) return;

            const labels = chartData.labels || [];
            const values = (chartData.data || []).map((value) => Number(value));
            const hasData = labels.length > 0 && values.some((value) => value > 0);
            updateAttendanceInsights(chartData, period);

            if (attendanceChartInstance) {
                attendanceChartInstance.destroy();
                attendanceChartInstance = null;
            }

            if (!hasData) {
                attendanceCanvas.classList.add('hidden');
                if (attendanceNoData) attendanceNoData.classList.remove('hidden');
                return;
            }

            attendanceCanvas.classList.remove('hidden');
            if (attendanceNoData) attendanceNoData.classList.add('hidden');

            const ctx = attendanceCanvas.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 320);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.24)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0.02)');

            attendanceChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Attendance',
                        data: values,
                        borderColor: '#0f766e',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#14b8a6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2
                    }, {
                        label: 'Target',
                        data: values.map(() => attendanceTarget),
                        borderColor: '#f59e0b',
                        borderDash: [6, 6],
                        borderWidth: 2,
                        pointRadius: 0,
                        pointHoverRadius: 0,
                        fill: false,
                        tension: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            filter(item) {
                                return item.dataset.label !== 'Target';
                            },
                            callbacks: {
                                label(context) {
                                    return `Attendance: ${formatPercent(context.parsed.y)}`;
                                },
                                afterLabel(context) {
                                    const detail = chartData.details?.[context.dataIndex];
                                    if (!detail) return null;
                                    const present = Number(detail.present || 0);
                                    const total = Number(detail.total || 0);
                                    const missing = Math.max(total - present, 0);
                                    if (!total) return 'No attendance records';
                                    return [
                                        `Present: ${present}`,
                                        `Not present: ${missing}`,
                                        `Tracked records: ${total}`,
                                        `Gap to target: ${Math.max(attendanceTarget - Number(detail.percentage || 0), 0).toFixed(1)}%`
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: labelColor },
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                color: labelColor,
                                callback(value) { return `${value}%`; }
                            },
                            grid: { color: gridColor }
                        }
                    }
                }
            });
        };

        renderAttendanceTrendChart(attendanceChartData, 'week');

        if (attendancePeriodSelect) {
            attendancePeriodSelect.addEventListener('change', async function () {
                const period = this.value;
                try {
                    const response = await fetch(`${attendanceDataUrl}?period=${encodeURIComponent(period)}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!response.ok) throw new Error(`Failed to load attendance data (${response.status})`);
                    const chartData = await response.json();
                    renderAttendanceTrendChart(chartData, period);
                } catch (error) {
                    console.error('Attendance chart load failed:', error);
                }
            });
        }

        const activityCanvas = document.getElementById('activityOverviewChart');
        if (activityCanvas) {
            new Chart(activityCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: activityOverviewData.map((item) => item.label),
                    datasets: [{
                        data: activityOverviewData.map((item) => Number(item.value || 0)),
                        backgroundColor: activityOverviewData.map((item) => item.color),
                        borderRadius: 12,
                        maxBarThickness: 42
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label(context) {
                                    return `${context.label}: ${Number(context.parsed.y || 0).toLocaleString()}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: labelColor },
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { color: labelColor },
                            grid: { color: gridColor }
                        }
                    }
                }
            });
        }
    });
</script>

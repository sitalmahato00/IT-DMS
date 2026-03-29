<?php

namespace App\Traits;

use App\Models\TimetableGapOverride;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

trait BuildsRoutineTimetable
{
    /**
     * Group slots by day name.
     */
    protected function buildTimetableByDay(Collection $timetableSlots, array $days): array
    {
        $timetableByDay = [];

        foreach ($days as $day) {
            $timetableByDay[$day] = $timetableSlots
                ->filter(fn ($slot) => $slot->day_of_week === $day)
                ->values();
        }

        return $timetableByDay;
    }

    /**
     * Build printable time rows and insert break rows between gaps.
     */
    protected function buildRoutineTimeRows(Collection $timetableSlots): Collection
    {
        $rows = $timetableSlots
            ->groupBy('start_time')
            ->map(function ($slotGroup, $startTime) {
                $resolvedEndTime = $slotGroup->max('end_time');

                return [
                    'key' => $startTime . '-' . $resolvedEndTime,
                    'start' => $startTime,
                    'end' => $resolvedEndTime,
                    'is_break' => false,
                ];
            })
            ->sortBy('start')
            ->values();

        if ($rows->isEmpty()) {
            return collect();
        }

        $rowsWithBreaks = collect();

        foreach ($rows as $index => $row) {
            $rowsWithBreaks->push($row);

            $nextRow = $rows->get($index + 1);

            if (!$nextRow || $row['end'] >= $nextRow['start']) {
                continue;
            }

            $rowsWithBreaks->push([
                'key' => 'break-' . $row['end'] . '-' . $nextRow['start'],
                'start' => $row['end'],
                'end' => $nextRow['start'],
                'is_break' => true,
            ]);
        }

        return $rowsWithBreaks->values();
    }

    /**
     * Build a slot matrix keyed by day and row key.
     */
    protected function buildRoutineSlotMatrix(array $days, array $timetableByDay, Collection $timeRows): array
    {
        $slotMatrix = [];

        foreach ($days as $day) {
            $daySlots = $timetableByDay[$day] ?? collect();

            foreach ($timeRows as $timeRow) {
                if ($timeRow['is_break']) {
                    $slotMatrix[$day][$timeRow['key']] = collect();
                    continue;
                }

                $slotMatrix[$day][$timeRow['key']] = $daySlots
                    ->filter(
                        fn ($slot) => $slot->start_time === $timeRow['start']
                            && $slot->end_time <= $timeRow['end']
                    )
                    ->sortBy('end_time')
                    ->values();
            }
        }

        return $slotMatrix;
    }

    /**
     * Build a lookup of break rows that should render as empty slots.
     */
    protected function buildRoutineGapOverrideMatrix(?string $semester, ?string $section = ''): array
    {
        if (blank($semester) || !Schema::hasTable('timetable_gap_overrides')) {
            return [];
        }

        return TimetableGapOverride::query()
            ->where('semester', (string) $semester)
            ->whereIn('section', array_values(array_unique([
                (string) ($section ?? ''),
                '',
            ])))
            ->get()
            ->groupBy('day_of_week')
            ->map(function ($overrides) {
                return $overrides->mapWithKeys(function ($override) {
                    return ['break-' . $override->start_time . '-' . $override->end_time => true];
                })->all();
            })
            ->all();
    }
}

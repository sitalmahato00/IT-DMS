<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Timetable - Semester {{ $semester }} @if($section)/ Section {{ $section }}@endif</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            font-size: 10px; 
            margin: 15px; 
            color: #111; 
            background: #fff;
        }
        
        /* Header */
        .header { text-align: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #1e40af; }
        .header h1 { font-size: 16px; color: #1e40af; margin-bottom: 3px; }
        .header h2 { font-size: 12px; color: #555; font-weight: normal; }
        .header .meta { font-size: 10px; color: #666; margin-top: 5px; }
        
        /* College Logo Placeholder */
        .logo-area { display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 10px; }
        .logo-placeholder { width: 40px; height: 40px; background: #1e40af; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px; }
        
        /* Table */
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th { 
            background: #1e40af; 
            color: white; 
            padding: 6px 4px; 
            text-align: center; 
            font-size: 9px; 
            font-weight: 600;
            text-transform: uppercase;
        }
        td { 
            border: 1px solid #d1d5db; 
            padding: 4px; 
            vertical-align: top; 
            min-height: 45px;
        }
        
        /* Time Column */
        .time-col { width: 70px; text-align: center; font-weight: bold; background: #f3f4f6; color: #374151; }
        
        /* Slot Styles */
        .slot { 
            padding: 3px 5px; 
            margin: 2px 0; 
            border-radius: 3px; 
            font-size: 9px; 
            line-height: 1.3;
            border-left: 3px solid;
        }
        
        /* Theory */
        .slot.theory { background: #dbeafe; border-left-color: #2563eb; color: #1e40af; }
        .slot.practical { background: #dcfce7; border-left-color: #16a34a; color: #166534; }
        .slot.tutorial { background: #fef9c3; border-left-color: #ca8a04; color: #854d0e; }
        .slot.elective { background: #f3e8ff; border-left-color: #9333ea; color: #6b21a8; }
        
        /* Lab Groups */
        .slot.lab-a { background: #ffe4e6; border-left-color: #e11d48; color: #9f1239; }
        .slot.lab-b { background: #ffedd5; border-left-color: #ea580c; color: #9a3412; }
        .slot.lab-c { background: #fef9c3; border-left-color: #ca8a04; color: #713f12; }
        .slot.lab-d { background: #dcfce7; border-left-color: #16a34a; color: #14532d; }
        
        /* Slot Content */
        .slot-subject { font-weight: 700; display: block; }
        .slot-teacher { font-size: 8px; opacity: 0.8; display: block; }
        .slot-room { font-size: 8px; opacity: 0.7; display: block; }
        .slot-lab { font-size: 7px; font-weight: 600; background: rgba(255,255,255,0.5); padding: 1px 3px; border-radius: 2px; }
        
        /* Empty Cell */
        .empty-cell { color: #9ca3af; font-style: italic; text-align: center; padding: 10px; }
        
        /* Break */
        .break-cell { background: #f3f4f6; text-align: center; color: #6b7280; font-size: 9px; font-style: italic; }
        
        /* Legend */
        .legend { 
            margin-top: 15px; 
            padding: 10px; 
            border: 1px solid #e5e7eb; 
            border-radius: 4px;
            page-break-inside: avoid;
        }
        .legend-title { font-weight: bold; font-size: 10px; margin-bottom: 5px; }
        .legend-items { display: flex; flex-wrap: wrap; gap: 10px; }
        .legend-item { display: flex; align-items: center; gap: 4px; font-size: 9px; }
        .legend-color { width: 12px; height: 12px; border-radius: 2px; }
        
        /* Footer */
        .footer { 
            margin-top: 15px; 
            text-align: right; 
            color: #9ca3af; 
            font-size: 9px; 
            border-top: 1px solid #e5e7eb; 
            padding-top: 8px;
        }
        
        /* Print Styles */
        @media print {
            body { margin: 5px; font-size: 9px; }
            th { font-size: 8px; padding: 4px 2px; }
            td { padding: 2px; min-height: 35px; }
            .slot { padding: 2px 3px; font-size: 8px; }
            .no-print { display: none !important; }
            .legend { margin-top: 10px; }
            @page { size: landscape; margin: 0.5in; }
        }
        
        /* Screen Only */
        @media screen {
            body { background: #f3f4f6; }
            .print-container { 
                max-width: 1400px; 
                margin: 0 auto; 
                background: white; 
                padding: 20px; 
                box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
            }
            .print-btn {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 10px 20px;
                background: #2563eb;
                color: white;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            }
            .print-btn:hover { background: #1d4ed8; }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <button onclick="window.print()" class="no-print print-btn">
            🖨️ Print Timetable
        </button>
        
        <div class="header">
            <div class="logo-area">
                <div class="logo-placeholder">IT</div>
            </div>
            <h1>Department of Information Technology (IT)</h1>
            <h2>Weekly Class Timetable</h2>
            <div class="meta">
                <strong>Semester {{ $semester }}</strong>
                @if($section) | Section {{ $section }} @endif
                | Academic Year: {{ date('Y') }}
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th class="time-col">Time</th>
                    <th>Sunday</th>
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednesday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                    <th>Saturday</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Build time slots from actual data
                    $timeSlots = [];
                    foreach($slotsByDay as $daySlots) {
                        foreach($daySlots as $slot) {
                            $key = $slot->start_time . '-' . $slot->end_time;
                            if(!isset($timeSlots[$key])) {
                                $timeSlots[$key] = [
                                    'start' => $slot->start_time,
                                    'end' => $slot->end_time,
                                ];
                            }
                        }
                    }
                    ksort($timeSlots);
                @endphp
                @forelse($timeSlots as $timeKey => $timeSlot)
                <tr>
                    <td class="time-col">
                        {{ \Carbon\Carbon::parse($timeSlot['start'])->format('g:i A') }}
                        <br>↓<br>
                        {{ \Carbon\Carbon::parse($timeSlot['end'])->format('g:i A') }}
                    </td>
                    @foreach(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                        @php
                            $daySlots = $slotsByDay[$day] ?? collect();
                            $timeSlotSlots = $daySlots->filter(function($slot) use ($timeSlot) {
                                return $slot->start_time >= $timeSlot['start'] && $slot->start_time < $timeSlot['end'];
                            });
                        @endphp
                        <td>
                            @if($timeSlotSlots->isNotEmpty())
                                @foreach($timeSlotSlots as $slot)
                                    @php
                                        $slotClass = $slot->slot_type;
                                        if($slot->slot_type === 'practical' && $slot->lab_group) {
                                            $slotClass .= ' lab-' . strtolower($slot->lab_group);
                                        }
                                    @endphp
                                    <div class="slot {{ $slotClass }}">
                                        <span class="slot-subject">{{ $slot->subject->subject_name ?? '—' }}</span>
                                        <span class="slot-teacher">
                                            @if($slot->teacher && $slot->teacher->user)
                                                {{ $slot->teacher->user->name }}
                                            @else
                                                {{ __('No teacher') }}
                                            @endif
                                        </span>
                                        <span class="slot-room">
                                            <i class="bi bi-geo-alt"></i> {{ $slot->room ?? 'TBA' }}
                                            @if($slot->lab_group)
                                                <span class="slot-lab">Lab {{ $slot->lab_group }}</span>
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            @else
                                <span class="empty-cell">—</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-cell">No scheduled classes</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Legend -->
        <div class="legend">
            <div class="legend-title">Legend / Color Key:</div>
            <div class="legend-items">
                <div class="legend-item">
                    <div class="legend-color" style="background: #dbeafe; border-left: 3px solid #2563eb;"></div>
                    <span>Theory</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #dcfce7; border-left: 3px solid #16a34a;"></div>
                    <span>Practical</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #fef9c3; border-left: 3px solid #ca8a04;"></div>
                    <span>Tutorial</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #f3e8ff; border-left: 3px solid #9333ea;"></div>
                    <span>Elective</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #ffe4e6; border-left: 3px solid #e11d48;"></div>
                    <span>Lab A</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #ffedd5; border-left: 3px solid #ea580c;"></div>
                    <span>Lab B</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #fef9c3; border-left: 3px solid #ca8a04;"></div>
                    <span>Lab C</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #dcfce7; border-left: 3px solid #16a34a;"></div>
                    <span>Lab D</span>
                </div>
            </div>
        </div>
        
        <div class="footer">
            Generated on: {{ now()->format('M d, Y g:i A') }} | IT Department | College ERP System
        </div>
    </div>
</body>
</html>

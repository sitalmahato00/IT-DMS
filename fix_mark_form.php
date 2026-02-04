<?php
// Fix mark upload form to include student_id
$c = file_get_contents("resources/views/admin/exam-show.blade.php");

// Fix the student row generation to include student_id as hidden field
$old1 = '<td class="px-3 py-2">
                    <input type="number" name="marks[${student.id}][marks_obtained]" value="${existingMark}" min="0" max="${fullMarks}" class="w-20 px-2 py-1 border border-gray-300 rounded text-xs text-center focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="0">
                </td>';

$new1 = '<td class="px-3 py-2">
                    <input type="hidden" name="marks[${student.id}][student_id]" value="${student.id}">
                    <input type="number" name="marks[${student.id}][marks_obtained]" value="${existingMark}" min="0" max="${fullMarks}" class="w-20 px-2 py-1 border border-gray-300 rounded text-xs text-center focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="0">
                </td>';

$c = str_replace($old1, $new1, $c);

file_put_contents("resources/views/admin/exam-show.blade.php", $c);
echo "Fixed mark upload form to include student_id\n";

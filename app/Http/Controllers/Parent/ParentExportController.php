<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParentExportController extends Controller
{
    /**
     * Export parent data to CSV
     * Exports children/students information
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        
        // Get parent's children (students associated with this parent)
        $parentModel = DB::table('parents')
            ->where('user_id', $user->id)
            ->first();
        
        $data = [];
        
        // Header row
        $data[] = ['Student Name', 'Email', 'Phone', 'Semester', 'Academic Year', 'Department', 'Roll No', 'Gender', 'Status'];
        
        if ($parentModel) {
            // Get associated students
            $children = DB::table('users')
                ->join('students', 'users.id', '=', 'students.user_id')
                ->where('students.parent_id', $parentModel->id)
                ->select('users.*', 'students.*')
                ->get();
            
            foreach ($children as $child) {
                $data[] = [
                    $child->name ?? '',
                    $child->email ?? '',
                    $child->phone ?? '',
                    $child->semester ?? '',
                    $child->academic_year ?? '',
                    $child->department ?? '',
                    $child->roll_no ?? '',
                    $child->gender ?? '',
                    $child->status ?? '',
                ];
            }
        }
        
        // If no children found
        if (count($data) === 1) {
            $data[] = ['No children associated with this parent account'];
        }
        
        // Generate CSV
        $filename = 'parent_export_' . date('Y-m-d_His') . '.csv';
        
        $handle = fopen('php://output', 'w');
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        return response()->make('', 200, $headers);
    }
}


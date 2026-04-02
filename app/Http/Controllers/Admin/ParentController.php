<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ParentModel;
use App\Models\Student;
use App\Notifications\ParentAccountNotification;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;

class ParentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = $request->get('q');
        $status = $request->get('status');
        $perPage = intval($request->get('per_page', 10)) ?: 10;

        $builder = User::where('role', 'parent')->with('parent');
        
        // Filter by user role
        if ($user->role === 'parent') {
            // Parents see only themselves
            $builder->where('id', $user->id);
        } elseif ($user->role === 'teacher' && $user->semester) {
            // Teachers see parents of their students (students in their semester)
            $studentIds = User::where('role', 'student')
                ->whereHas('student', function($q) use ($user) {
                    $q->where('semester', $user->semester);
                })
                ->pluck('id');
            
            if ($studentIds->isNotEmpty()) {
                $builder->whereIn('id', 
                    User::where('role', 'student')
                        ->whereIn('id', $studentIds)
                        ->whereNotNull('parent_id')
                        ->distinct('parent_id')
                        ->pluck('parent_id')
                );
            } else {
                $builder->whereRaw('1=0'); // No parents for students
            }
        }
        // Admin sees all parents (no additional filter)
        
        // Apply search filter (ensure we're still only searching parents)
        $builder->when($query, function($q) use ($query) {
                $q->where(function($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%")
                             ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->when($status, function($q) use ($status) {
                $q->whereHas('parent', function($p) use ($status) {
                    $p->where('status', $status);
                });
            })
            ->orderBy('created_at', 'desc');

        $parents = $builder->paginate($perPage)->withQueryString();

        // Attach children count so view can display it
        $parents->getCollection()->transform(function($p){
            $childrenStudents = Student::where('parent_id', $p->id)->with('user')->get();
            $p->children_count = $childrenStudents->count();
            $p->children = $childrenStudents->map(function($student) {
                return $student->user;
            })->filter();
            return $p;
        });

        return view('admin.parents', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','email', Rule::unique('users','email')],
            'phone' => 'nullable|digits:10',
            'parent_code' => ['nullable', 'string', 'max:20', Rule::unique('parents', 'parent_code')],
            'occupation' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'relationship' => 'nullable|string|max:100',
            'bio' => 'nullable|string',
            'status' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:4096',
            'children' => 'nullable|array',
            'children.*' => 'integer|exists:users,id',
            'gender' => 'nullable|string|max:20',
        ]);

        // Generate a temporary password
        $password = Str::random(10);

        $user = new User([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($password),
        ]);
        $user->role = 'parent';
        $user->save();

        // Create parent record with profile fields
        $parent = $user->parent()->create([
            'parent_code' => $data['parent_code'] ?? ('P' . str_pad($user->id, 4, '0', STR_PAD_LEFT)),
            'occupation' => $data['occupation'] ?? null,
            'gender' => $data['gender'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'department' => null,
            'bio' => $data['bio'] ?? null,
            'status' => $data['status'] ?? 'active',
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $parent->profile_photo_path = $path;
            $parent->save();
        }

        // Assign children
        if (!empty($data['children'])) {
            $childrenIds = array_filter($data['children'], fn($id) => !empty($id));
            if (!empty($childrenIds)) {
                Student::whereIn('user_id', $childrenIds)->update(['parent_id' => $user->id]);
            }
        }

        $credentialsEmailSent = true;
        try {
            $user->notify(new ParentAccountNotification($password));
        } catch (\Exception $e) {
            $credentialsEmailSent = false;
            \Illuminate\Support\Facades\Log::error('Failed to send parent notification: ' . $e->getMessage());
        }

        $message = $credentialsEmailSent
            ? 'Parent created successfully. Login credentials have been sent to the parent\'s email.'
            : 'Parent created successfully, but the credentials email could not be sent. Check mail settings and logs.';

        // Check if this is an AJAX request
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'email_sent' => $credentialsEmailSent,
                'parent' => $user
            ]);
        }

        return redirect()->route('admin.parents')->with($credentialsEmailSent ? 'success' : 'warning', $message);
    }

    public function lookupByEmail(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $parent = User::where('role', 'parent')
            ->with('parent')
            ->where('email', $data['email'])
            ->first();

        if (!$parent) {
            return response()->json([
                'found' => false,
            ]);
        }

        $children = Student::where('parent_id', $parent->id)
            ->with('user')
            ->get()
            ->map(fn ($student) => [
                'id' => $student->user?->id,
                'name' => $student->user?->name,
                'email' => $student->user?->email,
                'roll_no' => $student->roll_no,
                'semester' => $student->semester,
            ])
            ->values();

        return response()->json([
            'found' => true,
            'parent' => [
                'id' => $parent->id,
                'name' => $parent->name,
                'email' => $parent->email,
                'phone' => $parent->parent->phone ?? $parent->phone,
                'occupation' => $parent->parent->occupation ?? null,
                'address' => $parent->parent->address ?? null,
                'bio' => $parent->parent->bio ?? null,
                'status' => $parent->parent->status ?? 'active',
                'gender' => $parent->parent->gender ?? null,
                'parent_code' => $parent->parent->parent_code ?? null,
                'children_count' => $children->count(),
                'children' => $children,
            ],
        ]);
    }

    public function getStudents()
    {
        $user = auth()->user();
        
        $query = User::where('role', 'student');
        
        // Filter by user role
        if ($user->role === 'teacher' && $user->semester) {
            // Teachers see only students from their semester
            $query->whereHas('student', function($q) use ($user) {
                $q->where('semester', $user->semester);
            });
        } elseif ($user->role === 'parent') {
            // Parents see only their children
            $query->where('parent_id', $user->id);
        }
        // Admin sees all students
        
        $students = $query->select('id', 'name', 'email')
            ->orderBy('name')
            ->get()
            ->map(function($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'email' => $s->email,
                ];
            });
        
        return response()->json($students);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $parent = User::where('role', 'parent')->with('parent')->findOrFail($id);
        
        // Check authorization
        if ($user->role === 'parent' && $user->id !== $parent->id) {
            abort(403, 'Unauthorized');
        }
        
        $assignedChildren = Student::where('parent_id', $id)->with('user')->get()->map(function($student) {
            return [
                'id' => $student->user->id,
                'name' => $student->user->name,
                'email' => $student->user->email,
            ];
        })->toArray();
        
        // Get available students based on role
        $availableStudentsQuery = User::where('role', 'student');
        if ($user->role === 'teacher' && $user->semester) {
            $availableStudentsQuery->whereHas('student', function($q) use ($user) {
                $q->where('semester', $user->semester);
            });
        }
        $availableStudents = $availableStudentsQuery->orderBy('name')->get();

        $parentProfile = $parent->parent;
        return response()->json([
            'id' => $parent->id,
            'name' => $parent->name,
            'email' => $parent->email,
            'phone' => $parentProfile->phone ?? null,
            'occupation' => $parentProfile->occupation ?? null,
            'address' => $parentProfile->address ?? null,
            'bio' => $parentProfile->bio ?? null,
            'status' => $parentProfile->status ?? 'active',
            'profile_photo_path' => $parentProfile->profile_photo_path ?? null,
            'gender' => $parentProfile->gender ?? null,
            'parent_code' => $parentProfile->parent_code ?? null,
            'assigned_children' => $assignedChildren,
            'availableStudents' => $availableStudents,
        ]);
    }

    public function update(Request $request, $id)
    {
        $parent = User::where('role', 'parent')->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','email', Rule::unique('users','email')->ignore($id)],
            'phone' => 'nullable|digits:10',
            'occupation' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'bio' => 'nullable|string',
            'status' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:4096',
            'children' => 'nullable|array',
            'children.*' => 'integer|exists:users,id',
            'gender' => 'nullable|string|max:20',
        ]);

        $parent->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        // Update parent record with profile fields
        if ($parent->parent) {
            $parent->parent->update([
                'gender' => $data['gender'] ?? null,
                'phone' => $data['phone'] ?? null,
                'occupation' => $data['occupation'] ?? null,
                'address' => $data['address'] ?? null,
                'bio' => $data['bio'] ?? null,
                'status' => $data['status'] ?? 'active',
            ]);
        } else {
            // Create parent record if it doesn't exist
            $parent->parent()->create([
                'parent_code' => 'P' . str_pad($parent->id, 4, '0', STR_PAD_LEFT),
                'occupation' => $data['occupation'] ?? null,
                'gender' => $data['gender'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'bio' => $data['bio'] ?? null,
                'status' => $data['status'] ?? 'active',
            ]);
        }

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $parent->parent->profile_photo_path = $path;
            $parent->parent->save();
        }

        // Update children assignments
        // First, remove this parent from all students
        Student::where('parent_id', $parent->id)->update(['parent_id' => null]);

        // Then assign selected children
        if (!empty($data['children'])) {
            $childrenIds = array_filter($data['children'], fn($id) => !empty($id));
            if (!empty($childrenIds)) {
                Student::whereIn('user_id', $childrenIds)->update(['parent_id' => $parent->id]);
            }
        }

        // Check if this is an AJAX request
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Parent updated successfully',
                'parent' => $parent
            ]);
        }

        return redirect()->route('admin.parents')->with('success', 'Parent updated successfully');
    }

    public function destroy($id)
    {
        try {
            $parent = User::where('role', 'parent')->findOrFail($id);
            
            // Use database transaction to ensure data integrity
            \Illuminate\Support\Facades\DB::transaction(function () use ($parent) {
                // Unassign all children before deleting parent
                Student::where('parent_id', $parent->id)->update(['parent_id' => null]);
                
                // Delete the parent record (ParentModel)
                if ($parent->parent) {
                    $parent->parent->delete();
                }
                
                // Delete the user account
                $parent->delete();
            });
            
            // Always return JSON for AJAX requests
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Parent deleted successfully'
                ]);
            }
            
            return redirect()->route('admin.parents')->with('success', 'Parent deleted successfully');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error deleting parent: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting parent: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.parents')->with('error', 'Error deleting parent');
        }
    }

    public function export(Request $request)
    {
        try {
            $builder = User::where('role', 'parent')->with('parent');

            // Apply search filter
            if ($q = $request->input('q')) {
                $builder->where(function($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
                });
            }

            // Apply status filter
            if ($status = $request->input('status')) {
                $builder->whereHas('parent', function($s) use ($status) {
                    $s->where('status', $status);
                });
            }

            $rows = $builder->orderBy('created_at', 'desc')->get();

            // Prepare filter information
            $filterInfo = [];
            if ($request->input('q')) {
                $filterInfo[] = 'Search: ' . $request->input('q');
            }
            if ($request->input('status')) {
                $filterInfo[] = 'Status: ' . $request->input('status');
            }

            $filename = 'parents_export_' . date('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $columns = ['ID', 'Name', 'Parent ID', 'Email', 'Phone', 'Children', 'Role', 'Status'];

            $callback = function() use ($rows, $columns, $filterInfo) {
                $out = fopen('php://output', 'w');
                
                if (!$out) {
                    throw new \Exception('Failed to open output stream');
                }
                
                // Write BOM for UTF-8 CSV (Excel compatibility)
                fwrite($out, "\xEF\xBB\xBF");
                
                // Write metadata header
                fputcsv($out, ['PARENT EXPORT REPORT']);
                fputcsv($out, ['']);
                fputcsv($out, ['Export Date & Time: ' . date('Y-m-d H:i:s')]);
                fputcsv($out, ['Total Records: ' . count($rows)]);
                fputcsv($out, ['']);
                
                // Write filter information
                fputcsv($out, ['APPLIED FILTERS:']);
                foreach ($filterInfo as $filter) {
                    fputcsv($out, [$filter]);
                }
                fputcsv($out, ['']);
                fputcsv($out, ['']);
                
                // Write column headers
                fputcsv($out, $columns);
                
                // Write data rows
                foreach ($rows as $r) {
                    $childCount = Student::where('parent_id', $r->id)->count();
                    $line = [
                        $r->id,
                        $r->name,
                        $r->parent->parent_code ?? 'P' . str_pad($r->id, 4, '0', STR_PAD_LEFT),
                        $r->email,
                        $r->parent->phone ?? '',
                        $childCount,
                        ucfirst($r->role),
                        $r->parent->status ?? 'active',
                    ];
                    fputcsv($out, $line);
                }
                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('Parent export error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate export'], 500);
        }
    }

    public function print($id)
    {
        $parent = User::where('role','parent')->with('parent')->findOrFail($id);
        $children = User::where('role','student')
            ->whereHas('student', function($q) use ($id) {
                $q->where('parent_id', $id);
            })
            ->with('student')
            ->get();
        // Convert image to base64 for PDF
        $parent->photo_base64 = $this->getImageBase64($parent);
        return view('admin.partials.parent-print', compact('parent', 'children'));
    }

    public function download($id)
    {
        try {
            $parent = User::where('role','parent')->with('parent')->findOrFail($id);
            $children = User::where('role','student')
                ->whereHas('student', function($q) use ($id) {
                    $q->where('parent_id', $id);
                })
                ->with('student')
                ->get();
            // Convert image to base64 for PDF
            $parent->photo_base64 = $this->getImageBase64($parent);
            $pdf = Pdf::loadView('admin.partials.parent-print', compact('parent', 'children'));
            return $pdf->download('parent_' . $parent->id . '_' . Str::slug($parent->name) . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Parent download error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF'], 500);
        }
    }

    private function getImageBase64($user)
    {
        $path = null;
        
        if ($user->parent && $user->parent->profile_photo_path) {
            $path = $user->parent->profile_photo_path;
        } elseif ($user->profile_photo_path) {
            $path = $user->profile_photo_path;
        }

        if (!$path) {
            return null;
        }

        // Handle both with and without storage/ prefix
        if (strpos($path, 'storage/') === 0) {
            $fullPath = public_path($path);
        } else {
            $fullPath = public_path('storage/' . $path);
        }

        if (file_exists($fullPath)) {
            $data = file_get_contents($fullPath);
            $base64 = base64_encode($data);
            $mime = mime_content_type($fullPath);
            return 'data:' . $mime . ';base64,' . $base64;
        }

        return null;
    }

    /**
     * Print list of parents
     */
    public function printList(Request $request)
    {
        $query = $request->input('q');
        $status = $request->input('status');

        $parentsQuery = User::where('role', 'parent')
            ->with('parent')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->when($status, function ($q) use ($status) {
                $q->whereHas('parent', function ($p) use ($status) {
                    $p->where('status', $status);
                });
            })
            ->orderBy('name');

        $parents = $parentsQuery->get();
        
        // Attach student names to each parent
        $parents = $parents->map(function($parent) {
            $students = Student::where('parent_id', $parent->id)->with('user')->get();
            $studentNames = $students->map(function($student) {
                return $student->user->name ?? 'N/A';
            })->implode(', ');
            $parent->student_name = $students->isNotEmpty() ? $studentNames : 'N/A';
            return $parent;
        });

        $college = \App\Models\Department::first();

        return view('admin.print.parents-list', compact('parents', 'college'));
    }
}

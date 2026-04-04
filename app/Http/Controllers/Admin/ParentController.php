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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ParentController extends Controller
{
    private function parentFieldOptions(): array
    {
        return [
            'relationships' => ['Father', 'Mother', 'Guardian', 'Other'],
            'contactMethods' => ['call' => 'Call', 'email' => 'Email', 'sms' => 'SMS'],
            'notificationPreferences' => ['email' => 'Email', 'sms' => 'SMS'],
            'accessLevels' => ['view_only' => 'View Only', 'full_access' => 'Full Access'],
            'preferredLanguages' => ['English', 'Nepali', 'Hindi'],
            'profileVisibilities' => ['public' => 'Public', 'private' => 'Private'],
            'bloodGroups' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
            'statuses' => ['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended'],
        ];
    }

    private function availableStudentsFor(User $user)
    {
        $query = User::where('role', 'student')->with('student')->orderBy('name');

        if ($user->role === 'teacher' && $user->semester) {
            $query->whereHas('student', function ($q) use ($user) {
                $q->where('semester', $user->semester);
            });
        } elseif ($user->role === 'parent') {
            $query->where('parent_id', $user->id);
        }

        return $query->get();
    }

    private function syncChildren(User $parentUser, array $childrenIds = [], ?int $primaryChildUserId = null): void
    {
        Student::where('parent_id', $parentUser->id)->update(['parent_id' => null]);

        $childrenIds = array_values(array_unique(array_filter(array_map('intval', $childrenIds))));

        if (!empty($childrenIds)) {
            Student::whereIn('user_id', $childrenIds)->update(['parent_id' => $parentUser->id]);
        }

        $primaryChildUserId = $primaryChildUserId && in_array($primaryChildUserId, $childrenIds, true)
            ? $primaryChildUserId
            : (!empty($childrenIds) ? $childrenIds[0] : null);

        if ($parentUser->parent) {
            $parentUser->parent->primary_child_user_id = $primaryChildUserId;
            $parentUser->parent->save();
        }
    }

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

        // FIX #1: Eager load all students for all parents in ONE query (not N queries)
        $parentIds = $parents->getCollection()->pluck('id')->toArray();
        $allStudents = Student::whereIn('parent_id', $parentIds)->with('user')->get()->groupBy('parent_id');

        // Attach children count so view can display it
        $parents->getCollection()->transform(function($p) use ($allStudents){
            $childrenStudents = $allStudents->get($p->id, collect());
            $primaryChild = null;
            if (!empty($p->parent?->primary_child_user_id)) {
                $primaryChild = $childrenStudents->firstWhere('user_id', $p->parent->primary_child_user_id);
            }
            $p->children_count = $childrenStudents->count();
            $p->primary_child_name = $primaryChild?->user?->name;
            $p->children = $childrenStudents->map(function($student) {
                return $student->user;
            })->filter();
            return $p;
        });

        return view('admin.parents', compact('parents'));
    }

    public function create()
    {
        $user = auth()->user();
        $availableStudents = $this->availableStudentsFor($user)->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'roll_no' => $student->student?->roll_no,
                'semester' => $student->student?->semester,
                'program' => $student->student?->program,
            ];
        });

        return view('admin.parents.create', [
            'availableStudents' => $availableStudents,
            'fieldOptions' => $this->parentFieldOptions(),
        ]);
    }

    public function show($id)
    {
        $user = auth()->user();
        $parent = User::where('role', 'parent')->with('parent')->findOrFail($id);

        if ($user->role === 'parent' && $user->id !== $parent->id) {
            abort(403, 'Unauthorized');
        }

        $assignedChildren = Student::where('parent_id', $parent->id)->with('user')->get();
        $parentProfile = $parent->parent;

        return view('admin.parents.show', [
            'parent' => $parent,
            'parentProfile' => $parentProfile,
            'assignedChildren' => $assignedChildren,
            'primaryChildUserId' => $parentProfile?->primary_child_user_id,
            'fieldOptions' => $this->parentFieldOptions(),
        ]);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $parent = User::where('role', 'parent')->with('parent')->findOrFail($id);

        if ($user->role === 'parent' && $user->id !== $parent->id) {
            abort(403, 'Unauthorized');
        }

        $assignedChildren = Student::where('parent_id', $parent->id)->with('user')->get();
        $selectedChildren = $assignedChildren->pluck('user_id')->filter()->values();
        $availableStudents = $this->availableStudentsFor($user)->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'roll_no' => $student->student?->roll_no,
                'semester' => $student->student?->semester,
                'program' => $student->student?->program,
            ];
        });

        return view('admin.parents.edit', [
            'parent' => $parent,
            'parentProfile' => $parent->parent,
            'assignedChildren' => $assignedChildren,
            'selectedChildren' => $selectedChildren,
            'primaryChildUserId' => $parent->parent?->primary_child_user_id,
            'availableStudents' => $availableStudents,
            'fieldOptions' => $this->parentFieldOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')],
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'max:20'],
            'secondary_phone' => ['nullable', 'string', 'max:20'],
            'alternate_email' => ['nullable', 'email', 'max:191'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'parent_code' => ['nullable', 'string', 'max:20', Rule::unique('parents', 'parent_code')],
            'national_id_number' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
            'relationship' => ['nullable', 'string', 'max:60'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:120'],
            'state_province' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:120'],
            'employer_name' => ['nullable', 'string', 'max:150'],
            'work_address' => ['nullable', 'string'],
            'work_phone_number' => ['nullable', 'string', 'max:20'],
            'income_range' => ['nullable', 'string', 'max:60'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'medical_conditions' => ['nullable', 'string'],
            'emergency_notes' => ['nullable', 'string'],
            'bio' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['active', 'inactive', 'suspended'])],
            'profile_visibility' => ['nullable', Rule::in(['public', 'private'])],
            'preferred_language' => ['nullable', 'string', 'max:20'],
            'access_level' => ['nullable', Rule::in(['view_only', 'full_access'])],
            'preferred_contact_method' => ['nullable', Rule::in(['call', 'email', 'sms'])],
            'notification_preferences' => ['nullable', 'array'],
            'notification_preferences.*' => ['nullable', Rule::in(['email', 'sms'])],
            'portal_access' => ['nullable', 'boolean'],
            'emergency_contact_priority' => ['nullable', 'boolean'],
            'primary_child_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'profile_photo' => ['nullable', 'image', 'max:4096'],
            'id_proof_upload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
            'address_proof_upload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
            'children' => ['nullable', 'array'],
            'children.*' => ['integer', 'exists:users,id'],
            'gender' => ['nullable', 'string', 'max:20'],
        ]);

        $childrenIds = array_values(array_unique(array_filter(array_map('intval', $data['children'] ?? []))));
        $primaryChildUserId = !empty($data['primary_child_user_id']) ? (int) $data['primary_child_user_id'] : null;
        if ($primaryChildUserId && !in_array($primaryChildUserId, $childrenIds, true)) {
            $primaryChildUserId = !empty($childrenIds) ? $childrenIds[0] : null;
        }

        DB::beginTransaction();

        try {
            $password = Str::random(10);

            $user = new User([
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $data['username'] ?? null,
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($password),
            ]);
            $user->role = 'parent';
            $user->save();

            $parent = $user->parent()->create([
                'parent_code' => $data['parent_code'] ?? ('P' . str_pad($user->id, 4, '0', STR_PAD_LEFT)),
                'occupation' => $data['occupation'] ?? null,
                'national_id_number' => $data['national_id_number'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'relationship' => $data['relationship'] ?? null,
                'gender' => $data['gender'] ?? null,
                'phone' => $data['phone'] ?? null,
                'secondary_phone' => $data['secondary_phone'] ?? null,
                'alternate_email' => $data['alternate_email'] ?? null,
                'whatsapp_number' => $data['whatsapp_number'] ?? null,
                'preferred_contact_method' => $data['preferred_contact_method'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state_province' => $data['state_province'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'country' => $data['country'] ?? null,
                'employer_name' => $data['employer_name'] ?? null,
                'work_address' => $data['work_address'] ?? null,
                'work_phone_number' => $data['work_phone_number'] ?? null,
                'income_range' => $data['income_range'] ?? null,
                'blood_group' => $data['blood_group'] ?? null,
                'medical_conditions' => $data['medical_conditions'] ?? null,
                'emergency_notes' => $data['emergency_notes'] ?? null,
                'department' => null,
                'bio' => $data['bio'] ?? null,
                'status' => $data['status'] ?? 'active',
                'notification_preferences' => !empty($data['notification_preferences']) ? implode(',', $data['notification_preferences']) : null,
                'access_level' => $data['access_level'] ?? 'view_only',
                'portal_access' => $request->boolean('portal_access', true),
                'notes' => $data['notes'] ?? null,
                'preferred_language' => $data['preferred_language'] ?? null,
                'profile_visibility' => $data['profile_visibility'] ?? 'public',
                'emergency_contact_priority' => $request->boolean('emergency_contact_priority', false),
                'primary_child_user_id' => $primaryChildUserId,
            ]);

            if ($request->hasFile('profile_photo')) {
                $path = $request->file('profile_photo')->store('parents', 'public');
                $parent->profile_photo_path = $path;
            }

            if ($request->hasFile('id_proof_upload')) {
                $parent->id_proof_path = $request->file('id_proof_upload')->store('parents/documents', 'public');
            }

            if ($request->hasFile('address_proof_upload')) {
                $parent->address_proof_path = $request->file('address_proof_upload')->store('parents/documents', 'public');
            }

            $parent->save();
            $this->syncChildren($user, $childrenIds, $primaryChildUserId);

            DB::commit();

            $credentialsEmailSent = true;
            try {
                $user->notify(new ParentAccountNotification($password));
            } catch (\Exception $e) {
                $credentialsEmailSent = false;
                Log::error('Failed to send parent notification: ' . $e->getMessage());
            }

            $message = $credentialsEmailSent
                ? 'Parent created successfully. Login credentials have been sent to the parent\'s email.'
                : 'Parent created successfully, but the credentials email could not be sent. Check mail settings and logs.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'email_sent' => $credentialsEmailSent,
                    'parent' => $user,
                ]);
            }

            return redirect()->route('admin.parents.show', $user->id)->with($credentialsEmailSent ? 'success' : 'warning', $message);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Parent create failed: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create parent: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create parent: ' . $e->getMessage())
                ->withInput();
        }
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
                'program' => $student->program,
                'section' => $student->section,
                'academic_year' => $student->academic_year_bs ?: $student->academic_year,
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
                'national_id_number' => $parent->parent->national_id_number ?? null,
                'date_of_birth' => optional($parent->parent->date_of_birth)->format('Y-m-d'),
                'relationship' => $parent->parent->relationship ?? null,
                'secondary_phone' => $parent->parent->secondary_phone ?? null,
                'alternate_email' => $parent->parent->alternate_email ?? null,
                'whatsapp_number' => $parent->parent->whatsapp_number ?? null,
                'preferred_contact_method' => $parent->parent->preferred_contact_method ?? null,
                'city' => $parent->parent->city ?? null,
                'state_province' => $parent->parent->state_province ?? null,
                'postal_code' => $parent->parent->postal_code ?? null,
                'country' => $parent->parent->country ?? null,
                'employer_name' => $parent->parent->employer_name ?? null,
                'work_address' => $parent->parent->work_address ?? null,
                'work_phone_number' => $parent->parent->work_phone_number ?? null,
                'income_range' => $parent->parent->income_range ?? null,
                'blood_group' => $parent->parent->blood_group ?? null,
                'medical_conditions' => $parent->parent->medical_conditions ?? null,
                'emergency_notes' => $parent->parent->emergency_notes ?? null,
                'profile_visibility' => $parent->parent->profile_visibility ?? 'public',
                'preferred_language' => $parent->parent->preferred_language ?? null,
                'access_level' => $parent->parent->access_level ?? 'view_only',
                'portal_access' => (bool) ($parent->parent->portal_access ?? true),
                'notification_preferences' => array_values(array_filter(array_map('trim', explode(',', (string) ($parent->parent->notification_preferences ?? ''))))),
                'emergency_contact_priority' => (bool) ($parent->parent->emergency_contact_priority ?? false),
                'primary_child_user_id' => $parent->parent->primary_child_user_id ?? null,
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
        
        $students = $query->with('student')
            ->orderBy('name')
            ->get()
            ->map(function($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'email' => $s->email,
                    'roll_no' => $s->student?->roll_no,
                    'semester' => $s->student?->semester,
                    'program' => $s->student?->program,
                    'section' => $s->student?->section,
                    'academic_year' => $s->student?->academic_year_bs ?: $s->student?->academic_year,
                ];
            });
        
        return response()->json($students);
    }

    public function update(Request $request, $id)
    {
        $user = User::where('role', 'parent')->with('parent')->findOrFail($id);

        if (auth()->user()->role === 'parent' && auth()->id() !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:20'],
            'secondary_phone' => ['nullable', 'string', 'max:20'],
            'alternate_email' => ['nullable', 'email', 'max:191'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'parent_code' => ['nullable', 'string', 'max:20', Rule::unique('parents', 'parent_code')->ignore($user->parent?->id)],
            'national_id_number' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
            'relationship' => ['nullable', 'string', 'max:60'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:120'],
            'state_province' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:120'],
            'employer_name' => ['nullable', 'string', 'max:150'],
            'work_address' => ['nullable', 'string'],
            'work_phone_number' => ['nullable', 'string', 'max:20'],
            'income_range' => ['nullable', 'string', 'max:60'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'medical_conditions' => ['nullable', 'string'],
            'emergency_notes' => ['nullable', 'string'],
            'bio' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['active', 'inactive', 'suspended'])],
            'profile_visibility' => ['nullable', Rule::in(['public', 'private'])],
            'preferred_language' => ['nullable', 'string', 'max:20'],
            'access_level' => ['nullable', Rule::in(['view_only', 'full_access'])],
            'preferred_contact_method' => ['nullable', Rule::in(['call', 'email', 'sms'])],
            'notification_preferences' => ['nullable', 'array'],
            'notification_preferences.*' => ['nullable', Rule::in(['email', 'sms'])],
            'portal_access' => ['nullable', 'boolean'],
            'emergency_contact_priority' => ['nullable', 'boolean'],
            'primary_child_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'profile_photo' => ['nullable', 'image', 'max:4096'],
            'id_proof_upload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
            'address_proof_upload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
            'children' => ['nullable', 'array'],
            'children.*' => ['integer', 'exists:users,id'],
        ]);

        $childrenIds = array_values(array_unique(array_filter(array_map('intval', $data['children'] ?? []))));
        $primaryChildUserId = !empty($data['primary_child_user_id']) ? (int) $data['primary_child_user_id'] : null;
        if ($primaryChildUserId && !in_array($primaryChildUserId, $childrenIds, true)) {
            $primaryChildUserId = !empty($childrenIds) ? $childrenIds[0] : null;
        }

        DB::beginTransaction();

        try {
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $data['username'] ?? null,
                'phone' => $data['phone'] ?? null,
            ]);

            $parent = $user->parent ?: $user->parent()->make();
            $parent->fill([
                'parent_code' => $data['parent_code'] ?? ($parent->parent_code ?? ('P' . str_pad($user->id, 4, '0', STR_PAD_LEFT))),
                'occupation' => $data['occupation'] ?? null,
                'national_id_number' => $data['national_id_number'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'relationship' => $data['relationship'] ?? null,
                'gender' => $data['gender'] ?? null,
                'phone' => $data['phone'] ?? null,
                'secondary_phone' => $data['secondary_phone'] ?? null,
                'alternate_email' => $data['alternate_email'] ?? null,
                'whatsapp_number' => $data['whatsapp_number'] ?? null,
                'preferred_contact_method' => $data['preferred_contact_method'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state_province' => $data['state_province'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'country' => $data['country'] ?? null,
                'employer_name' => $data['employer_name'] ?? null,
                'work_address' => $data['work_address'] ?? null,
                'work_phone_number' => $data['work_phone_number'] ?? null,
                'income_range' => $data['income_range'] ?? null,
                'blood_group' => $data['blood_group'] ?? null,
                'medical_conditions' => $data['medical_conditions'] ?? null,
                'emergency_notes' => $data['emergency_notes'] ?? null,
                'department' => null,
                'bio' => $data['bio'] ?? null,
                'status' => $data['status'] ?? 'active',
                'notification_preferences' => !empty($data['notification_preferences']) ? implode(',', $data['notification_preferences']) : null,
                'access_level' => $data['access_level'] ?? 'view_only',
                'portal_access' => $request->boolean('portal_access', true),
                'notes' => $data['notes'] ?? null,
                'preferred_language' => $data['preferred_language'] ?? null,
                'profile_visibility' => $data['profile_visibility'] ?? 'public',
                'emergency_contact_priority' => $request->boolean('emergency_contact_priority', false),
                'primary_child_user_id' => $primaryChildUserId,
            ]);

            if ($request->hasFile('profile_photo')) {
                $parent->profile_photo_path = $request->file('profile_photo')->store('parents', 'public');
            }

            if ($request->hasFile('id_proof_upload')) {
                $parent->id_proof_path = $request->file('id_proof_upload')->store('parents/documents', 'public');
            }

            if ($request->hasFile('address_proof_upload')) {
                $parent->address_proof_path = $request->file('address_proof_upload')->store('parents/documents', 'public');
            }

            $parent->save();
            $this->syncChildren($user, $childrenIds, $primaryChildUserId);

            DB::commit();

            return redirect()->route('admin.parents.show', $user->id)->with('success', 'Parent updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Parent update failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to update parent: ' . $e->getMessage())
                ->withInput();
        }
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

            // FIX #2: Pre-calculate children counts for all parents (not N queries)
            $childrenCounts = Student::whereIn('parent_id', $rows->pluck('id'))
                ->groupBy('parent_id')
                ->selectRaw('parent_id, COUNT(*) as count')
                ->pluck('count', 'parent_id')
                ->toArray();

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
                    $childCount = $childrenCounts[$r->id] ?? 0;
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
        $parent = User::where('role', 'parent')->with('parent')->findOrFail($id);
        $children = Student::where('parent_id', $parent->id)->with('user')->get();
        $parent->photo_base64 = $this->getImageBase64($parent);
        return view('admin.partials.parent-print', compact('parent', 'children'));
    }

    public function download($id)
    {
        try {
            $parent = User::where('role', 'parent')->with('parent')->findOrFail($id);
            $children = Student::where('parent_id', $parent->id)->with('user')->get();
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

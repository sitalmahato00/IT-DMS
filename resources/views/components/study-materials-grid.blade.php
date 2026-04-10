@foreach($materials as $material)
    <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 material-card"
         data-type="{{ $material->document_type }}">
        <!-- Card Header with Importance Indicator (match notice style) -->
        <div class="h-2 {{ $material->document_type === 'lecture_notes' ? 'bg-gradient-to-r from-blue-500 to-cyan-500' : ($material->document_type === 'assignment' ? 'bg-gradient-to-r from-green-500 to-emerald-500' : ($material->document_type === 'assessment' ? 'bg-gradient-to-r from-orange-500 to-yellow-500' : ($material->document_type === 'lab_report' ? 'bg-gradient-to-r from-purple-500 to-pink-500' : 'bg-gradient-to-r from-gray-500 to-gray-600'))) }}"></div>
        <div class="p-6">
            <!-- Meta Row -->
            <div class="flex items-center justify-between mb-3">
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded text-xs font-medium text-gray-600">
                    <i class="bi bi-calendar3"></i>
                    {{ $material->semester ?? 'N/A' }} Semester
                </span>
                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-700">
                    {{ $material->document_type_text ?? 'Material' }}
                </span>
            </div>
            <!-- Title -->
            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                {{ $material->title }}
            </h3>
            <!-- Description Preview -->
            @if($material->description)
                <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                    {{ Str::limit(strip_tags($material->description), 120) }}
                </p>
            @endif
            <!-- Footer Row -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr($material->subject->subject_name ?? 'SU', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-900">{{ $material->subject->subject_name ?? 'General' }}</p>
                        <p class="text-xs text-gray-500">{{ $material->formatted_size ?? 'N/A' }}</p>
                    </div>
                </div>
                @if($material->file_path)
                    <a href="{{ route('admin.study-material.download', $material->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-colors">
                        <i class="bi bi-download"></i>
                        Download
                    </a>
                @endif
            </div>
        </div>
    </div>
@endforeach
@if($materials->isEmpty())
    <div class="col-span-full text-center py-12">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
            <i class="bi bi-book text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Study Materials Found</h3>
        <p class="text-gray-600">There are no study materials to display at the moment.</p>
    </div>
@endif


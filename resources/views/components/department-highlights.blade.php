<section id="departments" class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center text-red-600 mb-6">
            {{ __('Our Academic Departments') }}
        </h2>
        <p class="text-center text-gray-600 max-w-2xl mx-auto mb-10">
            {{ __('Explore the diverse academic departments that make up our institution') }}
        </p>
        
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($departments as $department)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-transform duration-300 transform hover:-translate-y-1 border border-gray-100">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-red-50 rounded-full w-12 h-12 flex items-center justify-center">
                                <i class="bi bi-building text-red-600 text-xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 ml-3">
                                {{ $department->name }}
                            </h3>
                        </div>
                        <p class="text-gray-600 mb-5 line-clamp-3">
                            {{ Str::limit($department->description ?? __('No description available'), 150) }}
                        </p>
                         @php
                             $departmentRouteExists = Illuminate\Support\Facades\Route::has('department.show');
                         @endphp
                         <a href="{{ $departmentRouteExists ? route('department.show', $department->id) : '#' }}" 
                            class="inline-flex items-center px-4 py-2 bg-red-50 text-red-600 font-medium rounded-lg hover:bg-red-100 transition-colors duration-200">
                             {{ __('Explore Department') }}
                             <i class="bi bi-arrow-right ms-2"></i>
                         </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
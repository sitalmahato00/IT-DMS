{{-- Flash Messages --}}
@if(session('success'))
    <div id="flash-success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded flex items-center gap-2">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
        <button onclick="document.getElementById('flash-success').remove()" class="ml-auto text-green-600 hover:text-green-800">
            <i class="bi bi-x"></i>
        </button>
    </div>
@endif

@if(session('error'))
    <div id="flash-error" class="bg-red-100 border border-red-400 text-blue-700 px-4 py-3 rounded flex items-center gap-2">
        <i class="bi bi-exclamation-circle-fill"></i>
        <span>{{ session('error') }}</span>
        <button onclick="document.getElementById('flash-error').remove()" class="ml-auto text-blue-600 hover:text-red-800">
            <i class="bi bi-x"></i>
        </button>
    </div>
@endif

@error('title')
    <div class="bg-red-100 border border-red-400 text-blue-700 px-4 py-3 rounded">{{ $message }}</div>
@enderror

{{-- Persist flash messages across page refresh using localStorage --}}
<script>
    // Show flash message and store in localStorage
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            // Use global showToast from layout
            if (typeof showToast === 'function') {
                showToast('{{ session('success') }}', 'success');
            }
            localStorage.setItem('studyMaterialToast', JSON.stringify({
                type: 'success',
                message: '{{ session('success') }}'
            }));
        @endif

        @if(session('error'))
            if (typeof showToast === 'function') {
                showToast('{{ session('error') }}', 'error');
            }
            localStorage.setItem('studyMaterialToast', JSON.stringify({
                type: 'error',
                message: '{{ session('error') }}'
            }));
        @endif

        // Check for stored toast on page load/refresh
        const storedToast = localStorage.getItem('studyMaterialToast');
        if (storedToast && typeof showToast === 'function') {
            const toast = JSON.parse(storedToast);
            showToast(toast.message, toast.type);
            localStorage.removeItem('studyMaterialToast');
        }
    });
</script>




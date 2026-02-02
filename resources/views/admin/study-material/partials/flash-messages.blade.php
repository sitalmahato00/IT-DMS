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
    <div id="flash-error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded flex items-center gap-2">
        <i class="bi bi-exclamation-circle-fill"></i>
        <span>{{ session('error') }}</span>
        <button onclick="document.getElementById('flash-error').remove()" class="ml-auto text-red-600 hover:text-red-800">
            <i class="bi bi-x"></i>
        </button>
    </div>
@endif

@error('title')
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ $message }}</div>
@enderror

{{-- Persist flash messages across page refresh using localStorage --}}
<script>
    // Show flash message and store in localStorage
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            showToast('success', '{{ session('success') }}');
            localStorage.setItem('studyMaterialToast', JSON.stringify({
                type: 'success',
                message: '{{ session('success') }}'
            }));
        @endif

        @if(session('error'))
            showToast('error', '{{ session('error') }}');
            localStorage.setItem('studyMaterialToast', JSON.stringify({
                type: 'error',
                message: '{{ session('error') }}'
            }));
        @endif

        // Check for stored toast on page load/refresh
        const storedToast = localStorage.getItem('studyMaterialToast');
        if (storedToast) {
            const toast = JSON.parse(storedToast);
            showToast(toast.type, toast.message);
            localStorage.removeItem('studyMaterialToast');
        }
    });

    function showToast(type, message) {
        // Create toast element
        const toast = document.createElement('div');
        toast.id = 'toast-' + Date.now();
        toast.className = type === 'success'
            ? 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded flex items-center gap-2 shadow-lg z-50'
            : 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded flex items-center gap-2 shadow-lg z-50';
        toast.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'}-fill"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.remove()" class="ml-2 hover:opacity-75">
                <i class="bi bi-x"></i>
            </button>
        `;
        document.body.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 5000);
    }
</script>


{{-- Modal JavaScript --}}
<script>
    // Modal functions are defined in add-modal.blade.php
    // Keep this for delete modal functionality only
    
    /**
     * Show delete confirmation
     */
    function confirmDelete(materialId, materialTitle) {
        const form = document.getElementById('deleteForm');
        const titleSpan = document.getElementById('deleteMaterialTitle');

        form.action = `/admin/study-material/${materialId}`;
        titleSpan.textContent = materialTitle;

        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Close delete modal
     */
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close delete modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });

    // Close delete modal on background click
    document.getElementById('deleteModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>


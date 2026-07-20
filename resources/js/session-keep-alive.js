/**
 * Session Keep-Alive Module
 * 
 * Prevents 419 PAGE EXPIRED errors by periodically refreshing the CSRF token
 * and keeping the user session alive during idle periods.
 */

export function initializeSessionKeepAlive() {
    // Configuration (in milliseconds)
    const REFRESH_INTERVAL = 45 * 60 * 1000; // Refresh every 45 minutes (before 1 hour typical idle limit)
    const CSRF_TOKEN_META = 'csrf-token';
    
    /**
     * Refresh CSRF token by making a lightweight request
     */
    async function refreshCsrfToken() {
        try {
            // Make a simple GET request to refresh the session
            const response = await fetch(window.location.href, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            if (response.ok) {
                // Update CSRF token from meta tag if available
                const newToken = document.querySelector(`meta[name="${CSRF_TOKEN_META}"]`)?.getAttribute('content');
                if (newToken) {
                    updateCsrfToken(newToken);
                }
                console.log('[Session Keep-Alive] CSRF token refreshed successfully');
            }
        } catch (error) {
            console.error('[Session Keep-Alive] Failed to refresh CSRF token:', error);
        }
    }

    /**
     * Update CSRF token in meta tag and all forms
     */
    function updateCsrfToken(token) {
        // Update meta tag
        let metaTag = document.querySelector(`meta[name="${CSRF_TOKEN_META}"]`);
        if (metaTag) {
            metaTag.setAttribute('content', token);
        }

        // Update all form inputs with name="csrf_token"
        document.querySelectorAll('input[name="csrf_token"]').forEach(input => {
            input.value = token;
        });

        // Update all form inputs with name="_token"
        document.querySelectorAll('input[name="_token"]').forEach(input => {
            input.value = token;
        });

        // Update Axios default header if available (from bootstrap)
        if (window.axios) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
        }
    }

    /**
     * Reset the keep-alive timer on user activity
     */
    function resetKeepAliveTimer() {
        // Clear existing interval
        if (window.__sessionKeepAliveTimer) {
            clearInterval(window.__sessionKeepAliveTimer);
        }

        // Set new interval
        window.__sessionKeepAliveTimer = setInterval(refreshCsrfToken, REFRESH_INTERVAL);
    }

    /**
     * Track user activity and reset timer
     */
    function setupActivityListeners() {
        const events = ['mousedown', 'keydown', 'scroll', 'touchstart', 'click'];
        
        events.forEach(event => {
            document.addEventListener(event, resetKeepAliveTimer, true);
        });
    }

    // Only initialize for authenticated users (when CSRF token exists)
    const csrfToken = document.querySelector(`meta[name="${CSRF_TOKEN_META}"]`);
    if (csrfToken) {
        setupActivityListeners();
        resetKeepAliveTimer();
        console.log('[Session Keep-Alive] Initialized - CSRF token will refresh every 45 minutes');
    }
}

// Auto-initialize when module is imported
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeSessionKeepAlive);
} else {
    initializeSessionKeepAlive();
}

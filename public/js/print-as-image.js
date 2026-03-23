/**
 * PrintAsImage - Global Service for High-DPI PNG Printing
 * 
 * Converts any DOM element to high-DPI PNG images formatted as A4 pages,
 * with automatic multipage slicing and Ctrl+P integration.
 * 
 * Usage:
 *   window.PrintAsImage.init({ quality: 300, scale: 2 })
 *   window.PrintAsImage.register(element, { multipage: true })
 *   window.PrintAsImage.render(handle)
 *   window.PrintAsImage.print(handle)
 */

(function(window) {
    'use strict';

    if (window.PrintAsImage) {
        console.warn('[PrintAsImage] Already initialized');
        return;
    }

    /**
     * Main PrintAsImage engine
     */
    const PrintAsImage = {
        // Configuration
        config: {
            quality: 300,           // DPI equivalent
            scale: 2,               // Scale multiplier for html2canvas
            backgroundColor: '#ffffff',
            logging: false,
            a4WidthMm: 190,        // Account for margins
            a4HeightMm: 277,       // Usable height
            dpi: 96,               // Standard screen DPI
        },

        // State
        elements: {},           // Registered elements
        activeHandle: null,     // Currently active element
        renderQueue: {},        // Pending renders
        isIntercepting: false,  // Ctrl+P interception active

        /**
         * Initialize the service with optional config
         */
        init: function(config) {
            if (config) {
                Object.assign(this.config, config);
            }
            this.setupCtrlPInterception();
            this.observeResizing();
            console.log('[PrintAsImage] Initialized', this.config);
            return this;
        },

        /**
         * Register a container element for printing
         * 
         * @param {HTMLElement} el - Container with data-print child
         * @param {Object} options - Configuration options
         * @returns {string} handle - Unique ID for this element
         */
        register: function(el, options) {
            if (!el) {
                console.error('[PrintAsImage] Container element required');
                return null;
            }

            const handle = el.dataset.printId || el.id;
            if (!handle) {
                console.warn('[PrintAsImage] No ID found on element');
                return null;
            }

            this.elements[handle] = {
                container: el,              // Parent container
                contentEl: el.querySelector('[data-print]'), // Content to print
                renderContainer: el.querySelector(`#printPages-${handle}`),
                options: Object.assign({
                    template: 'generic',
                    college: null,
                    meta: {},
                    multipage: true,
                    includeHeader: true,
                    includeFooter: true,
                }, options),
                rendered: false,
                renderTime: null,
                images: [],
                pageCount: 0,
            };

            // Auto-set first registered element as active
            if (!this.activeHandle) {
                this.setActive(handle);
            }

            console.log('[PrintAsImage] Registered:', handle);
            return handle;
        },

        /**
         * Set which element is the active print target
         */
        setActive: function(handle) {
            if (this.elements[handle]) {
                this.activeHandle = handle;
                console.log('[PrintAsImage] Active:', handle);
            } else {
                console.warn('[PrintAsImage] Invalid handle:', handle);
            }
            return this;
        },

        /**
         * Get the active element's handle
         */
        getActive: function() {
            return this.activeHandle;
        },

        /**
         * Get registered element info
         */
        getElement: function(handle) {
            return this.elements[handle] || null;
        },

        /**
         * Render element to high-DPI PNG images
         */
        render: async function(handle) {
            const entry = this.elements[handle];
            if (!entry) {
                console.error('[PrintAsImage] Handle not found:', handle);
                return false;
            }

            if (!window.html2canvas) {
                console.error('[PrintAsImage] html2canvas library not loaded');
                return false;
            }

            console.log('[PrintAsImage] Rendering:', handle);
            const startTime = performance.now();

            try {
                // Get content element
                const contentEl = entry.contentEl;
                if (!contentEl) {
                    console.error('[PrintAsImage] Content element [data-print] not found');
                    return false;
                }

                // Clone element to avoid mutations during capture
                const cloneEl = contentEl.cloneNode(true);
                cloneEl.style.visibility = 'hidden';
                cloneEl.style.position = 'absolute';
                cloneEl.style.top = '-9999px';
                cloneEl.style.left = '-9999px';
                document.body.appendChild(cloneEl);

                // Capture at high DPI
                const canvas = await html2canvas(cloneEl, {
                    scale: this.config.scale,
                    backgroundColor: this.config.backgroundColor,
                    useCORS: true,
                    logging: this.config.logging,
                    imageTimeout: 15000,
                    allowTaint: true,
                });

                // Clean up clone
                document.body.removeChild(cloneEl);

                // Enhance with header/footer if configured
                const enhancedCanvas = this.injectHeaderFooter(canvas, entry);

                // Slice into A4 pages if multipage enabled
                entry.images = this.sliceIntoPages(enhancedCanvas, entry.options.multipage);
                entry.pageCount = entry.images.length;
                entry.rendered = true;
                entry.renderTime = performance.now() - startTime;

                // Populate render container
                this.populateContainer(handle);

                console.log(
                    '[PrintAsImage] Rendered:',
                    handle,
                    `(${entry.pageCount} pages, ${Math.round(entry.renderTime)}ms)`
                );

                return true;
            } catch (err) {
                console.error('[PrintAsImage] Render failed:', err);
                entry.rendered = false;
                return false;
            }
        },

        /**
         * Inject header/footer with college details and page numbers
         */
        injectHeaderFooter: function(canvas, entry) {
            if (!entry.options.includeHeader && !entry.options.includeFooter) {
                return canvas;
            }

            const mmToPx = this.config.dpi / 25.4;
            const headerHeight = entry.options.includeHeader ? Math.round(15 * mmToPx) : 0;
            const footerHeight = entry.options.includeFooter ? Math.round(10 * mmToPx) : 0;

            // Create new canvas with space for header/footer
            const newCanvas = document.createElement('canvas');
            newCanvas.width = canvas.width;
            newCanvas.height = canvas.height + headerHeight + footerHeight;

            const ctx = newCanvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, newCanvas.width, newCanvas.height);

            // Draw original content
            ctx.drawImage(canvas, 0, headerHeight);

            // Draw header
            if (entry.options.includeHeader && entry.options.college) {
                ctx.fillStyle = '#333333';
                ctx.font = `bold ${Math.round(12 * mmToPx / 16)}px Arial`;
                const college = entry.options.college;
                const headerText = college.name || 'College';
                ctx.fillText(headerText, Math.round(10 * mmToPx), Math.round(10 * mmToPx));
            }

            // Draw footer with page numbers (will be updated per-page in sliceIntoPages)
            if (entry.options.includeFooter) {
                ctx.fillStyle = '#666666';
                ctx.font = `${Math.round(10 * mmToPx / 16)}px Arial`;
                const footerText = entry.options.meta?.title || 'Document';
                const footerY = newCanvas.height - Math.round(3 * mmToPx);
                ctx.fillText(footerText, Math.round(10 * mmToPx), footerY);
            }

            return newCanvas;
        },

        /**
         * Slice canvas into A4-sized PNG images
         */
        sliceIntoPages: function(canvas, multipage) {
            if (multipage === false) {
                // Single page: just convert to image
                return [canvas.toDataURL('image/png')];
            }

            const images = [];
            const mmToPx = this.config.dpi / 25.4;
            const pageHeight = this.config.a4HeightMm * mmToPx * this.config.scale;
            const totalPages = Math.ceil(canvas.height / pageHeight);

            for (let page = 0; page < totalPages; page++) {
                const pageCanvas = document.createElement('canvas');
                pageCanvas.width = canvas.width;
                pageCanvas.height = Math.min(pageHeight, canvas.height - page * pageHeight);

                const ctx = pageCanvas.getContext('2d');
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, pageCanvas.width, pageCanvas.height);

                // Draw content for this page
                ctx.drawImage(
                    canvas,
                    0, page * pageHeight,                           // source position
                    canvas.width, pageCanvas.height,                // source size
                    0, 0,                                           // destination position
                    canvas.width, pageCanvas.height                 // destination size
                );

                // Draw page number
                ctx.fillStyle = '#999999';
                ctx.font = `${Math.round(10 * mmToPx / 16)}px Arial`;
                const pageText = `Page ${page + 1} of ${totalPages}`;
                const textX = pageCanvas.width - Math.round(60 * mmToPx);
                const textY = pageCanvas.height - Math.round(5 * mmToPx);
                ctx.fillText(pageText, textX, textY);

                images.push(pageCanvas.toDataURL('image/png'));
            }

            return images;
        },

        /**
         * Populate the render container with generated images
         */
        populateContainer: function(handle) {
            const entry = this.elements[handle];
            if (!entry || !entry.renderContainer) return;

            entry.renderContainer.innerHTML = '';
            entry.images.forEach((imgData, idx) => {
                const img = document.createElement('img');
                img.src = imgData;
                img.dataset.page = idx + 1;
                img.alt = `Page ${idx + 1}`;
                img.style.display = 'block';
                img.style.width = '100%';
                img.style.height = 'auto';
                img.style.pageBreakAfter = 'always';
                img.style.pageBreakInside = 'avoid';
                img.style.margin = '0';
                img.style.padding = '0';
                entry.renderContainer.appendChild(img);
            });
        },

        /**
         * Trigger print dialog
         */
        print: function(handle) {
            handle = handle || this.activeHandle;
            const entry = this.elements[handle];

            if (!entry || !entry.rendered) {
                console.warn('[PrintAsImage] No rendered content to print:', handle);
                return false;
            }

            console.log('[PrintAsImage] Printing:', handle);
            window.print();
            return true;
        },

        /**
         * Set up Ctrl+P / Cmd+P interception
         */
        setupCtrlPInterception: function() {
            if (this.isIntercepting) return;
            this.isIntercepting = true;

            const self = this;
            document.addEventListener('keydown', function(e) {
                // Ctrl+P or Cmd+P
                if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                    e.preventDefault();

                    // Find target to print
                    let handle = self.activeHandle;
                    if (!handle) {
                        // Try to find nearest print container
                        let el = e.target;
                        while (el && !el.dataset.printId) {
                            el = el.parentElement;
                        }
                        if (el) {
                            handle = el.dataset.printId;
                        }
                    }

                    if (handle) {
                        console.log('[PrintAsImage] Ctrl+P triggered for:', handle);
                        self.render(handle).then((success) => {
                            if (success) {
                                self.print(handle);
                            }
                        });
                    }
                }
            });

            console.log('[PrintAsImage] Ctrl+P interception active');
        },

        /**
         * Observe window resizing and re-render if needed
         */
        observeResizing: function() {
            const self = this;
            let resizeTimeout;

            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    if (self.activeHandle) {
                        console.log('[PrintAsImage] Window resized, re-rendering active element');
                        self.render(self.activeHandle);
                    }
                }, 500);
            });
        },

        /**
         * Clear all rendered images and reset state
         */
        clear: function(handle) {
            if (handle) {
                const entry = this.elements[handle];
                if (entry) {
                    if (entry.renderContainer) {
                        entry.renderContainer.innerHTML = '';
                    }
                    entry.images = [];
                    entry.rendered = false;
                    entry.pageCount = 0;
                }
            } else {
                // Clear all
                Object.keys(this.elements).forEach((h) => {
                    this.clear(h);
                });
            }
            console.log('[PrintAsImage] Cleared:', handle || 'all');
            return this;
        },

        /**
         * Get statistics
         */
        stats: function(handle) {
            if (handle) {
                const entry = this.elements[handle];
                return entry ? {
                    handle: handle,
                    rendered: entry.rendered,
                    pageCount: entry.pageCount,
                    renderTime: entry.renderTime,
                    lastRender: new Date(Date.now() - (entry.renderTime || 0)).toISOString(),
                } : null;
            } else {
                const stats = {};
                Object.keys(this.elements).forEach((h) => {
                    stats[h] = this.stats(h);
                });
                return stats;
            }
        },
    };

    // Expose globally
    window.PrintAsImage = PrintAsImage;

    // Auto-init on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            console.log('[PrintAsImage] Auto-initialized on document ready');
        });
    }

})(window);

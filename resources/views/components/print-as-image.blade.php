@props([
    'id' => 'print-' . uniqid(),
    'template' => 'generic',
    'college' => null,
    'meta' => [],
    'multipage' => true,
])

<div id="print-container-{{ $id }}" class="print-as-image-wrapper">
    <!-- Screen view: normal display -->
    <div class="print-screen-content" data-print="{{ $id }}">
        {{ $slot }}
    </div>

    <!-- Print view: hidden from screen, shown at print -->
    <div id="printPages-{{ $id }}" class="print-pages-container" data-print-pages="{{ $id }}"></div>
</div>

<style scoped>
    /* Screen view - show content normally */
    @media screen {
        #print-container-{{ $id }} .print-screen-content {
            display: block;
        }
        #print-container-{{ $id }} #printPages-{{ $id }} {
            display: none;
        }
    }

    /* Print view - show only PNG container, hide original content */
    @media print {
        #print-container-{{ $id }} .print-screen-content {
            display: none !important;
        }
        #print-container-{{ $id }} #printPages-{{ $id }} {
            display: block;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        #print-container-{{ $id }} #printPages-{{ $id }} img {
            width: 100%;
            height: auto;
            display: block;
            page-break-after: always;
            page-break-inside: avoid;
            margin: 0;
            padding: 0;
        }
    }

    /* A4 size configuration */
    @media print {
        @page {
            size: A4;
            margin: 0;
        }
        #print-container-{{ $id }} {
            margin: 0;
            padding: 0;
        }
    }
</style>

@once
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    @push('scripts')
    <script>
        // Initialize global PrintAsImage service if not already done
        if (typeof window.PrintAsImage === 'undefined') {
            window.PrintAsImage = {
                config: {
                    quality: 300,
                    scale: 2,
                    backgroundColor: '#ffffff',
                    logging: false,
                },
                elements: {},
                activeHandle: null,

                init: function(config) {
                    Object.assign(this.config, config);
                    this.setupCtrlPInterception();
                    return this;
                },

                register: function(el, options) {
                    const handle = el.dataset.printId || el.id;
                    this.elements[handle] = {
                        element: el,
                        container: document.querySelector(`#printPages-${handle}`),
                        options: options || {},
                        rendered: false,
                        images: []
                    };
                    // Auto-set first registered as active
                    if (!this.activeHandle) {
                        this.setActive(handle);
                    }
                    return handle;
                },

                setActive: function(handle) {
                    if (this.elements[handle]) {
                        this.activeHandle = handle;
                        console.log('[PrintAsImage] Active:', handle);
                    }
                    return this;
                },

                render: async function(handle) {
                    const entry = this.elements[handle];
                    if (!entry) {
                        console.error('[PrintAsImage] Handle not found:', handle);
                        return;
                    }

                    console.log('[PrintAsImage] Rendering:', handle);
                    try {
                        const contentEl = entry.element.querySelector('[data-print]');
                        if (!contentEl) {
                            console.error('[PrintAsImage] Content element not found');
                            return;
                        }

                        // Capture at high DPI
                        const canvas = await html2canvas(contentEl, {
                            scale: this.config.scale,
                            backgroundColor: this.config.backgroundColor,
                            useCORS: true,
                            logging: this.config.logging,
                            imageTimeout: 15000,
                        });

                        // Slice into A4 pages if multipage enabled
                        entry.images = this.sliceIntoPages(canvas, entry.options.multipage !== false);
                        entry.rendered = true;

                        // Render images into container
                        entry.container.innerHTML = '';
                        entry.images.forEach((imgData, idx) => {
                            const img = document.createElement('img');
                            img.src = imgData;
                            img.dataset.page = idx + 1;
                            img.alt = `Page ${idx + 1}`;
                            entry.container.appendChild(img);
                        });

                        console.log('[PrintAsImage] Rendered:', handle, `(${entry.images.length} pages)`);
                    } catch (err) {
                        console.error('[PrintAsImage] Render failed:', err);
                    }
                },

                sliceIntoPages: function(canvas, multipage) {
                    const images = [];

                    if (!multipage) {
                        // Single page: just convert to image
                        images.push(canvas.toDataURL('image/png'));
                        return images;
                    }

                    // A4 dimensions (in pixels at current scale)
                    // A4: 210mm × 297mm, assuming 96 DPI
                    const a4WidthMm = 190; // Account for margins
                    const a4HeightMm = 277;
                    const dpi = 96;
                    const mmToPx = dpi / 25.4;
                    const pageHeight = a4HeightMm * mmToPx * (this.config.scale / 1);

                    const totalPages = Math.ceil(canvas.height / pageHeight);

                    for (let page = 0; page < totalPages; page++) {
                        // Create new canvas for this page
                        const pageCanvas = document.createElement('canvas');
                        pageCanvas.width = canvas.width;
                        pageCanvas.height = Math.min(pageHeight, canvas.height - page * pageHeight);

                        const ctx = pageCanvas.getContext('2d');
                        ctx.drawImage(
                            canvas,
                            0, page * pageHeight, // source position
                            canvas.width, pageCanvas.height, // source size
                            0, 0, // destination position
                            canvas.width, pageCanvas.height // destination size
                        );

                        images.push(pageCanvas.toDataURL('image/png'));
                    }

                    return images;
                },

                print: function(handle) {
                    const entry = this.elements[handle];
                    if (!entry || !entry.rendered) {
                        console.warn('[PrintAsImage] No rendered content to print:', handle);
                        return;
                    }

                    console.log('[PrintAsImage] Printing:', handle);
                    window.print();
                },

                setupCtrlPInterception: function() {
                    const self = this;
                    document.addEventListener('keydown', function(e) {
                        // Ctrl+P or Cmd+P
                        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                            e.preventDefault();

                            // Find active element or use activeHandle
                            let handle = self.activeHandle;
                            if (!handle) {
                                // Try to find nearest print container
                                let el = document.activeElement;
                                while (el && !el.dataset.printId) {
                                    el = el.parentElement;
                                }
                                if (el) {
                                    handle = el.dataset.printId;
                                }
                            }

                            if (handle) {
                                self.render(handle).then(() => {
                                    self.print(handle);
                                });
                            }
                        }
                    });
                }
            };

            window.PrintAsImage.init();
        }
    </script>
    @endpush
@endonce

<script>
    // Register this element when component loads
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('print-container-{{ $id }}');
        if (container && window.PrintAsImage) {
            const contentEl = container.querySelector('[data-print]');
            if (contentEl) {
                const options = {
                    template: '{{ $template }}',
                    college: @json($college),
                    meta: @json($meta),
                    multipage: {{ $multipage ? 'true' : 'false' }},
                };
                contentEl.dataset.printId = '{{ $id }}';
                window.PrintAsImage.register(container, options);
            }
        }
    });
</script>


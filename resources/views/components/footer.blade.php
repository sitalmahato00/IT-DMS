<footer class="bg-gradient-to-br from-slate-900 to-slate-950 text-white py-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-red-600 opacity-5 rounded-full -mr-48 -mt-48 blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-red-600 opacity-5 rounded-full -ml-48 -mb-48 blur-3xl"></div>

    <div class="max-w-7xl mx-auto relative z-10">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12 pb-12 border-b border-slate-700">
            
            <!-- Company Info -->
            <div class="group">
                <div class="flex items-center gap-2 mb-6">
                    <div class="bg-gradient-to-br from-red-600 to-red-700 text-white font-bold px-4 py-2 rounded-lg text-lg shadow-lg group-hover:shadow-xl group-hover:scale-105 transition-all duration-300">
                        <span class="flex items-center gap-2">
                            <i class="bi bi-calendar-check"></i>
                            {{ $companyName }}
                        </span>
                    </div>
                </div>
                <p class="text-slate-400 text-sm leading-relaxed group-hover:text-slate-300 transition-colors">
                    {{ $companyDescription }}
                </p>
            </div>

            <!-- Product Links -->
            <div>
                <h3 class="font-bold text-white mb-6 text-lg relative inline-block">
                    Product
                    <span class="absolute bottom-0 left-0 w-12 h-1 bg-gradient-to-r from-red-600 to-red-700 rounded-full"></span>
                </h3>
                <ul class="space-y-3">
                    @foreach($footerLinks['Product'] as $link)
                        <li>
                            <a 
                                href="{{ $link['url'] }}" 
                                class="text-slate-400 hover:text-white hover:translate-x-1 transition-all duration-300 inline-flex items-center gap-2 group"
                            >
                                <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Departments Links -->
            <div>
                <h3 class="font-bold text-white mb-6 text-lg relative inline-block">
                    Departments
                    <span class="absolute bottom-0 left-0 w-12 h-1 bg-gradient-to-r from-red-600 to-red-700 rounded-full"></span>
                </h3>
                <ul class="space-y-3">
                    <li>
                        <a 
                            href="{{ url('/#departments') }}" 
                            class="text-slate-400 hover:text-white hover:translate-x-1 transition-all duration-300 inline-flex items-center gap-2 group"
                        >
                            <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                            {{ __('All Departments') }}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Resources Links -->
            <div>
                <h3 class="font-bold text-white mb-6 text-lg relative inline-block">
                    Resources
                    <span class="absolute bottom-0 left-0 w-12 h-1 bg-gradient-to-r from-red-600 to-red-700 rounded-full"></span>
                </h3>
                <ul class="space-y-3">
                    @foreach($footerLinks['Resources'] as $link)
                        <li>
                            <a 
                                href="{{ $link['url'] }}" 
                                class="text-slate-400 hover:text-white hover:translate-x-1 transition-all duration-300 inline-flex items-center gap-2 group"
                            >
                                <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h3 class="font-bold text-white mb-6 text-lg relative inline-block">
                    Contact Us
                    <span class="absolute bottom-0 left-0 w-12 h-1 bg-gradient-to-r from-red-600 to-red-700 rounded-full"></span>
                </h3>
                <ul class="space-y-3">
                    @foreach($footerLinks['Contact Us'] as $link)
                        <li>
                            <a 
                                href="{{ $link['url'] }}" 
                                class="text-slate-400 hover:text-white hover:translate-x-1 transition-all duration-300 inline-flex items-center gap-2 group text-sm"
                            >
                                <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Bottom Footer -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
            <!-- Copyright -->
            <p class="text-slate-500 text-sm hover:text-slate-400 transition-colors">
                © {{ $year }} Springfield College. All rights reserved.
            </p>

            <!-- Social Links -->
            <div class="flex items-center gap-8">
                <a href="https://facebook.com" class="text-slate-400 hover:text-red-600 hover:scale-125 transition-all duration-300 group" title="Facebook">
                    <div class="p-2 hover:bg-red-600 hover:bg-opacity-20 rounded-lg transition-all duration-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </div>
                </a>
                <a href="https://twitter.com" class="text-slate-400 hover:text-red-600 hover:scale-125 transition-all duration-300 group" title="Twitter">
                    <div class="p-2 hover:bg-red-600 hover:bg-opacity-20 rounded-lg transition-all duration-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 002.856-3.915 10 10 0 01-2.836.775 4.958 4.958 0 002.165-2.724c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417a9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </div>
                </a>
                <a href="https://instagram.com" class="text-slate-400 hover:text-red-600 hover:scale-125 transition-all duration-300 group" title="Instagram">
                    <div class="p-2 hover:bg-red-600 hover:bg-opacity-20 rounded-lg transition-all duration-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2"/>
                            <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z" fill="none" stroke="currentColor" stroke-width="2"/>
                            <circle cx="17.5" cy="6.5" r="1.5" fill="currentColor"/>
                        </svg>
                    </div>
                </a>
            </div>
        </div>
    </div>
</footer>
            
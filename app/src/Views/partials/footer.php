<?php
/**
 * Footer partial - Site footer with navigation and copyright.
 */
?>
    <!-- Footer -->
    <div class="w-full px-4 md:px-12 lg:px-24 flex flex-col justify-end items-center gap-2.5 overflow-hidden">
        <div class="self-stretch pt-8 md:pt-10 border-t-2 border-slate-800 flex flex-col justify-start items-start gap-6 md:gap-10">
            <!-- Footer Navigation -->
            <div class="self-stretch flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 lg:gap-0 overflow-hidden">
                <div class="flex flex-wrap justify-start items-center gap-2 md:gap-5">
                    <a href="/" class="p-2 md:p-2.5 border-b-2 border-slate-800 flex justify-center items-center gap-2.5 transition-colors duration-200">
                        <span class="text-center text-slate-800 text-lg md:text-2xl font-normal">Home</span>
                    </a>
                    <a href="/jazz" class="p-2 md:p-2.5 border-b-2 border-transparent hover:border-slate-800 flex justify-center items-center gap-2.5 transition-all duration-200">
                        <span class="text-center text-slate-800 text-lg md:text-2xl font-normal">Jazz</span>
                    </a>
                    <a href="/dance" class="p-2 md:p-2.5 border-b-2 border-transparent hover:border-slate-800 flex justify-center items-center gap-2.5 transition-all duration-200">
                        <span class="text-center text-slate-800 text-lg md:text-2xl font-normal">Dance</span>
                    </a>
                    <a href="/history" class="p-2 md:p-2.5 border-b-2 border-transparent hover:border-slate-800 flex justify-center items-center gap-2.5 transition-all duration-200">
                        <span class="text-center text-slate-800 text-lg md:text-2xl font-normal">History</span>
                    </a>
                    <a href="/restaurant" class="p-2 md:p-2.5 border-b-2 border-transparent hover:border-slate-800 flex justify-center items-center gap-2.5 transition-all duration-200">
                        <span class="text-center text-slate-800 text-lg md:text-2xl font-normal">Restaurants</span>
                    </a>
                    <a href="/storytelling" class="p-2 md:p-2.5 border-b-2 border-transparent hover:border-slate-800 flex justify-center items-center gap-2.5 transition-all duration-200">
                        <span class="text-center text-slate-800 text-lg md:text-2xl font-normal">Storytelling</span>
                    </a>
                </div>
                <!-- Social Links -->
                <div class="flex justify-start items-center gap-6">
                    <a href="#" class="h-10 md:h-12 p-[5px] rounded-[20px] flex justify-start items-center gap-2.5 hover:text-pink-700 transition-colors duration-200">
                        <svg class="w-7 h-7 md:w-9 md:h-9" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073z"/>
                        </svg>
                        <span class="text-center text-slate-800 text-lg md:text-2xl font-normal">Instagram</span>
                    </a>
                </div>
            </div>

            <!-- Copyright Bar -->
            <div class="self-stretch px-4 md:px-7 py-8 md:py-12 bg-slate-800 rounded-tl-xl rounded-tr-xl md:rounded-tl-2xl md:rounded-tr-2xl flex flex-col md:flex-row justify-between items-center gap-4 overflow-hidden">
                <div class="flex justify-start items-center gap-3.5">
                    <!-- Logo -->
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-stone-100 rounded-full flex items-center justify-center">
                        <span class="text-pink-500 text-xl md:text-2xl">🌷</span>
                    </div>
                    <a href="#" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;" class="py-2.5 flex justify-start items-center gap-3 md:gap-5 text-white hover:text-pink-300 transition-colors duration-200">
                        <span class="text-center text-lg md:text-2xl font-normal">BACK TO THE TOP</span>
                        <svg class="w-5 h-5 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                        </svg>
                    </a>
                </div>
                <div class="flex justify-center items-center gap-2.5">
                    <span class="text-center text-white text-base md:text-2xl font-normal">© 2026 The Festival. All rights reserved.</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


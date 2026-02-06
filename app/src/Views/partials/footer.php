<?php
/**
 * Footer partial - Site footer with navigation and copyright.
 */
?>
    <!-- Footer -->
    <footer class="w-full px-2 sm:px-4 md:px-8 lg:px-16 xl:px-24 flex flex-col justify-end items-center gap-2.5 overflow-hidden" role="contentinfo">
        <div class="self-stretch pt-4 sm:pt-6 md:pt-8 lg:pt-10 border-t-2 border-slate-800 flex flex-col justify-start items-start gap-4 sm:gap-6 md:gap-8 lg:gap-10">
            <!-- Footer Navigation -->
            <div class="self-stretch flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 sm:gap-4 lg:gap-0 overflow-hidden">
                <nav class="flex flex-wrap justify-start items-center gap-1 sm:gap-2 md:gap-3 lg:gap-5" aria-label="Footer navigation">
                    <a href="/" class="p-1.5 sm:p-2 md:p-2.5 border-b-2 border-slate-800 flex justify-center items-center gap-2.5 transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2 rounded">
                        <span class="text-center text-slate-800 text-sm sm:text-base md:text-lg lg:text-xl font-normal">Home</span>
                    </a>
                    <a href="/jazz" class="p-1.5 sm:p-2 md:p-2.5 border-b-2 border-transparent hover:border-slate-800 flex justify-center items-center gap-2.5 transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2 rounded">
                        <span class="text-center text-slate-800 text-sm sm:text-base md:text-lg lg:text-xl font-normal">Jazz</span>
                    </a>
                    <a href="/dance" class="p-1.5 sm:p-2 md:p-2.5 border-b-2 border-transparent hover:border-slate-800 flex justify-center items-center gap-2.5 transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2 rounded">
                        <span class="text-center text-slate-800 text-sm sm:text-base md:text-lg lg:text-xl font-normal">Dance</span>
                    </a>
                    <a href="/history" class="p-1.5 sm:p-2 md:p-2.5 border-b-2 border-transparent hover:border-slate-800 flex justify-center items-center gap-2.5 transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2 rounded">
                        <span class="text-center text-slate-800 text-sm sm:text-base md:text-lg lg:text-xl font-normal">History</span>
                    </a>
                    <a href="/restaurant" class="p-1.5 sm:p-2 md:p-2.5 border-b-2 border-transparent hover:border-slate-800 flex justify-center items-center gap-2.5 transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2 rounded">
                        <span class="text-center text-slate-800 text-sm sm:text-base md:text-lg lg:text-xl font-normal">Restaurants</span>
                    </a>
                    <a href="/storytelling" class="p-1.5 sm:p-2 md:p-2.5 border-b-2 border-transparent hover:border-slate-800 flex justify-center items-center gap-2.5 transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2 rounded">
                        <span class="text-center text-slate-800 text-sm sm:text-base md:text-lg lg:text-xl font-normal">Storytelling</span>
                    </a>
                </nav>
                <!-- Social Links -->
                <nav class="flex justify-start items-center gap-3 sm:gap-4 md:gap-6" aria-label="Social media">
                    <a href="#" class="h-8 sm:h-10 md:h-12 p-1 sm:p-[5px] rounded-[20px] flex justify-start items-center gap-1.5 sm:gap-2 md:gap-2.5 hover:text-pink-700 transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2" aria-label="Follow us on Instagram">
                        <i data-lucide="instagram" class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 lg:w-9 lg:h-9" aria-hidden="true"></i>
                        <span class="text-center text-slate-800 text-sm sm:text-base md:text-lg lg:text-xl font-normal">Instagram</span>
                    </a>
                </nav>
            </div>

            <!-- Copyright Bar -->
            <div class="self-stretch px-2 sm:px-4 md:px-6 lg:px-7 py-4 sm:py-6 md:py-8 lg:py-12 bg-slate-800 rounded-tl-lg rounded-tr-lg sm:rounded-tl-xl sm:rounded-tr-xl md:rounded-tl-2xl md:rounded-tr-2xl flex flex-col md:flex-row justify-between items-center gap-3 sm:gap-4 overflow-hidden">
                <div class="flex justify-start items-center gap-2 sm:gap-3 md:gap-3.5">
                    <img
                        class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 lg:w-12 lg:h-12"
                        src="/assets/Icons/Logo.svg"
                        alt="" role="presentation">
                    <button type="button" onclick="window.scrollTo({top: 0, behavior: 'smooth'});" class="group py-1.5 sm:py-2 md:py-2.5 inline-flex justify-start items-center gap-2 sm:gap-3 md:gap-5 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2 rounded">
                        <span class="text-center text-white text-sm sm:text-base md:text-lg lg:text-xl font-normal">BACK TO THE TOP</span>
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 lg:w-9 lg:h-9 fill-none text-white transition-transform duration-200 group-hover:scale-125" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M12 4l7 7m-7-7l-7 7m7-7v16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
                <p class="flex justify-center items-center gap-2.5">
                    <small class="text-center text-white text-xs sm:text-sm md:text-base lg:text-xl font-normal">© 2026 The Festival. All rights reserved.</small>
                </p>
            </div>
        </div>
    </footer>

    <script src="/assets/js/lucide-init.js"></script>
</body>
</html>

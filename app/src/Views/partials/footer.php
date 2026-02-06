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
                        <i data-lucide="instagram" class="w-7 h-7 md:w-9 md:h-9"></i>
                        <span class="text-center text-slate-800 text-lg md:text-2xl font-normal">Instagram</span>
                    </a>
                </div>
            </div>

            <!-- Copyright Bar -->
            <div class="self-stretch px-4 md:px-7 py-8 md:py-12 bg-slate-800 rounded-tl-xl rounded-tr-xl md:rounded-tl-2xl md:rounded-tr-2xl flex flex-col md:flex-row justify-between items-center gap-4 overflow-hidden">
                <div class="flex justify-start items-center gap-3.5">
                    <img
                        class="w-10 h-10 md:w-12 md:h-12"
                        src="/Icons/Logo.svg"
                        alt="Haarlem Festival logo">
                    <a href="#" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;" class="group py-2.5 inline-flex justify-start items-center gap-5">
                        <span class="text-center text-white text-2xl font-normal">BACK TO THE TOP</span>
                        <svg class="w-9 h-9 fill-none text-white transition-transform duration-200 group-hover:scale-125" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 4l7 7m-7-7l-7 7m7-7v16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>
                <div class="flex justify-center items-center gap-2.5">
                    <span class="text-center text-white text-base md:text-2xl font-normal">© 2026 The Festival. All rights reserved.</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
</body>
</html>

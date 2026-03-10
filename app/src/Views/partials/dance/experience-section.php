<?php
/**
 * Dance festival experience slider.
 */
?>

<section class="w-full bg-sand py-14">
    <div class="w-full px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24">
        <div class="text-center mb-8">
            <p class="text-[14px] uppercase tracking-[0.35em] text-black mb-3">
                BEYOND THE STAGE
            </p>

            <h2 class="text-[56px] font-extrabold leading-none text-black mb-4">
                The Festival Experience
            </h2>

            <p class="max-w-[760px] mx-auto text-[20px] leading-[1.5] text-[#4b5563]">
                Lights, lasers, dancers, food, friends and memories. Explore every corner
                of the festival grounds and discover something new with each beat.
            </p>
        </div>

        <div class="relative w-full overflow-hidden rounded-[28px]">
            <div id="dance-experience-slider" class="flex transition-transform duration-700 ease-in-out">
                <img
                        src="/assets/Image/dance/black.jpg"
                        alt="Festival-goers dancing with hands in the air"
                        class="w-full h-[540px] object-cover flex-shrink-0">

                <img
                        src="/assets/Image/dance/bubbles.jpg"
                        alt="Festival bubbles and lights"
                        class="w-full h-[540px] object-cover flex-shrink-0">

                <img
                        src="/assets/Image/dance/dancerrrr.jpg"
                        alt="People dancing at the festival"
                        class="w-full h-[540px] object-cover flex-shrink-0">

                <img
                        src="/assets/Image/dance/more.jpg"
                        alt="Festival stage and crowd"
                        class="w-full h-[540px] object-cover flex-shrink-0">

                <img
                        src="/assets/Image/dance/more dancers.jpg"
                        alt="More dancers at the festival"
                        class="w-full h-[540px] object-cover flex-shrink-0">
            </div>

            <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex gap-3 z-10">
                <button class="dance-dot w-3 h-3 rounded-full bg-white/60" data-slide="0"></button>
                <button class="dance-dot w-3 h-3 rounded-full bg-white/60" data-slide="1"></button>
                <button class="dance-dot w-3 h-3 rounded-full bg-white/60" data-slide="2"></button>
                <button class="dance-dot w-3 h-3 rounded-full bg-white/60" data-slide="3"></button>
                <button class="dance-dot w-3 h-3 rounded-full bg-white/60" data-slide="4"></button>
            </div>
        </div>
    </div>
</section>

<script>
    (() => {
        const slider = document.getElementById('dance-experience-slider');
        if (!slider) return;

        const dots = document.querySelectorAll('.dance-dot');
        const totalSlides = slider.children.length;
        let currentIndex = 0;

        function updateSlider(index) {
            slider.style.transform = `translateX(-${index * 100}%)`;

            dots.forEach((dot, i) => {
                dot.classList.toggle('bg-white', i === index);
                dot.classList.toggle('bg-white/60', i !== index);
            });
        }

        dots.forEach((dot) => {
            dot.addEventListener('click', () => {
                currentIndex = Number(dot.dataset.slide);
                updateSlider(currentIndex);
            });
        });

        updateSlider(currentIndex);

        setInterval(() => {
            currentIndex = (currentIndex + 1) % totalSlides;
            updateSlider(currentIndex);
        }, 4000);
    })();
</script>

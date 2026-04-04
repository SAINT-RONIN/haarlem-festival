document.querySelectorAll('[data-jazz-artists-carousel]').forEach((carousel) => {
    const pages = Array.from(carousel.querySelectorAll('[data-jazz-artists-page]'));
    if (pages.length === 0) {
        return;
    }

    const section = carousel.closest('section');
    const controls = section?.querySelector('[data-jazz-artists-controls]') ?? null;
    const previousButton = controls?.querySelector('[data-jazz-artists-prev]') ?? null;
    const nextButton = controls?.querySelector('[data-jazz-artists-next]') ?? null;
    const status = controls?.querySelector('[data-jazz-artists-status]') ?? null;
    const dots = Array.from(controls?.querySelectorAll('[data-jazz-artists-dot]') ?? []);
    let currentIndex = 0;

    const render = () => {
        pages.forEach((page, index) => {
            page.classList.toggle('hidden', index !== currentIndex);
        });

        dots.forEach((dot, index) => {
            dot.classList.toggle('bg-royal-blue', index === currentIndex);
            dot.classList.toggle('bg-zinc-300', index !== currentIndex);
            dot.classList.toggle('hover:bg-zinc-400', index !== currentIndex);
            dot.setAttribute('aria-current', index === currentIndex ? 'true' : 'false');
        });

        if (previousButton instanceof HTMLButtonElement) {
            previousButton.disabled = currentIndex === 0;
        }

        if (nextButton instanceof HTMLButtonElement) {
            nextButton.disabled = currentIndex === pages.length - 1;
        }

        if (status instanceof HTMLElement) {
            const activePage = pages[currentIndex];
            const startArtist = activePage.getAttribute('data-start-artist') ?? '0';
            const endArtist = activePage.getAttribute('data-end-artist') ?? '0';
            const totalArtists = carousel.getAttribute('data-total-artists') ?? '0';
            status.textContent = `Artist ${startArtist}-${endArtist} of ${totalArtists}`;
        }
    };

    previousButton?.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex -= 1;
            render();
        }
    });

    nextButton?.addEventListener('click', () => {
        if (currentIndex < pages.length - 1) {
            currentIndex += 1;
            render();
        }
    });

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            const index = Number(dot.getAttribute('data-page-index') ?? '0');
            if (Number.isNaN(index) || index < 0 || index >= pages.length) {
                return;
            }

            currentIndex = index;
            render();
        });
    });

    render();
});

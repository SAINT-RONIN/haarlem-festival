(function () {
    var el = document.querySelector('[data-bg-url]');
    if (!el) return;
    var url = el.getAttribute('data-bg-url');
    if (!url) return;
    el.style.backgroundImage =
        "linear-gradient(to bottom, rgba(0,0,0,0.25), rgba(0,0,0,0.65)), url('" + url + "')";
})();

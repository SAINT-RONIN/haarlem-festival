/**
 * CMS Artist Form JavaScript.
 *
 * Initializes TinyMCE rich-text editor and Lucide icons for the artist create/edit form.
 */

document.addEventListener('DOMContentLoaded', function () {
    initTinyMce();
    if (typeof lucide !== 'undefined') lucide.createIcons();
});

function initTinyMce() {
    if (typeof tinymce === 'undefined') return;
    tinymce.init({
        selector: 'textarea[data-tinymce]',
        height: 220,
        menubar: false,
        plugins: 'lists link',
        toolbar: 'undo redo | bold italic underline | bullist numlist | link | removeformat',
        setup: function (editor) {
            editor.on('change', function () { editor.save(); });
        }
    });
}

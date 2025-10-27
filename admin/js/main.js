import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

document.addEventListener('DOMContentLoaded', function() {
    // Initialize CKEditor for all elements with class 'ckeditor-textarea'
    document.querySelectorAll('.ckeditor-textarea').forEach(textarea => {
        ClassicEditor
            .create(textarea, {
                // toolbar: [
                //     'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo'
                // ],
            })
            .catch(error => {
                console.error('CKEditor initialization error:', error);
            });
    });
});

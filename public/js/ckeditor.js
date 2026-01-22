document.addEventListener('DOMContentLoaded', function() {
    function initializeCKEditor(textareaElement) {
        if (textareaElement && !textareaElement.classList.contains('ckeditor-initialized')) {
            var uniqueId = 'ckeditor-' + Date.now() + '-' + Math.random().toString().substring(2, 5);
            textareaElement.id = uniqueId;
            textareaElement.classList.add('ckeditor-initialized');
            CKEDITOR.replace(uniqueId);
        }
    }

    // Function to initialize CKEditor for description fields
    function initializeCKEditorForDescriptionFields() {
        document.querySelectorAll('textarea[name*="contentsections"][name*="[description]"]').forEach(function(textarea) {
            initializeCKEditor(textarea);
        });
    }

    // Initialize CKEditor for existing description fields
    initializeCKEditorForDescriptionFields();

    // Initialize CKEditor for dynamically added description fields
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                // Check if the added node is a textarea or has textarea children
                if (node.nodeType === Node.ELEMENT_NODE) {
                    if (node.tagName === 'TEXTAREA' && node.name.includes('contentsections') && node.name.includes('[description]')) {
                        initializeCKEditor(node);
                    } else if (node.querySelectorAll) {
                        node.querySelectorAll('textarea[name*="contentsections"][name*="[description]"]').forEach(function(textarea) {
                            initializeCKEditor(textarea);
                        });
                    }
                }
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

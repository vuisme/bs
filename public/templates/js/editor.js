/**
 * Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/* exported initSample */

if (CKEDITOR.env.ie && CKEDITOR.env.version < 9)
    CKEDITOR.tools.enableHtml5Elements(document);

// The trick to keep the editor in the sample quite small
// unless user specified own height.
CKEDITOR.config.height = '400';
CKEDITOR.config.width = 'auto';

var initBasic = (function () {
    var wysiwygareaAvailable = isWysiwygareaAvailable();

    return function () {
        var editorElement = CKEDITOR.document.getById('ckeditor');

        if (wysiwygareaAvailable) {
            CKEDITOR.replace('ckeditor', {});
        } else {
            editorElement.setAttribute('contenteditable', 'true');
        }

    };

    function isWysiwygareaAvailable() {
        // If in development mode, then the wysiwygarea must be available.
        // Split REV into two strings so builder does not replace it :D.
        if (CKEDITOR.revision == ('%RE' + 'V%')) {
            return true;
        }

        return !!CKEDITOR.plugins.get('wysiwygarea');
    }
})();


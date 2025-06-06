CKEDITOR.editorConfig = function (config) {
    config.toolbarGroups = [
        {name: 'document', groups: ['mode', 'document', 'doctools']},
        {name: 'clipboard', groups: ['clipboard', 'undo']},
        {name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing']},
        {name: 'forms', groups: ['forms']},
        '/',
        {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
        {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']},
        {name: 'links', groups: ['links']},
        {name: 'insert', groups: ['insert']},
        '/',
        {name: 'styles', groups: ['styles']},
        {name: 'colors', groups: ['colors']},
        {name: 'tools', groups: ['tools']},
        {name: 'others', groups: ['others']},
        '/',
        {name: 'about', groups: ['about']}
    ];

    config.removeButtons = 'About,ShowBlocks,Maximize,Flash,Image,Anchor,Unlink,BidiLtr,BidiRtl,Language,Link,Iframe,Blockquote,Outdent,Indent,CreateDiv,RemoveFormat,CopyFormatting,HiddenField,ImageButton,Button,Select,Textarea,TextField,Radio,Checkbox,Form';
};
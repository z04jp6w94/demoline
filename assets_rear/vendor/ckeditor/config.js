/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function (config) {
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    // config.uiColor = '#AADC6E';
    config.toolbarGroups = [
        {name: 'document', groups: ['mode', 'document', 'doctools']},
        {name: 'clipboard', groups: ['clipboard', 'undo']},
        {name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing']},
        {name: 'forms', groups: ['forms']},
        {name: 'insert', groups: ['insert']},
        {name: 'links', groups: ['links']},
        {name: 'tools', groups: ['tools']},
        '/',
        {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
        {name: 'styles', groups: ['styles']},
        {name: 'colors', groups: ['colors']},
        {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']},
        '/',
        {name: 'others', groups: ['others']},
        {name: 'about', groups: ['about']}
    ];
    config.removeButtons = 'Save,NewPage,Preview,Print,Templates,Cut,Copy,Undo,Redo,Form,TextField,Select,Button,ImageButton,Subscript,Superscript,NumberedList,BulletedList,Indent,Outdent,Blockquote,CreateDiv,BidiLtr,BidiRtl,Language,Anchor,Flash,HorizontalRule,PageBreak,Iframe,Font,Styles,BGColor,ShowBlocks,HiddenField,Textarea,Radio,Checkbox,About,Underline';

    config.width = 550; //寬度
    config.height = 300; //高度
    config.toolbarCanCollapse = false; //是否能夠收縮工具欄
    //config.resize_enabled = false; //是否能夠改變畫面內容大小

    //Plugins
    config.extraPlugins = "youtube";
    config.allowedContent = true;
    //UploadFile
    config.filebrowserUploadUrl = "/assets_rear/vendor/ckeditor/uploadFile.php?sourceType=file";
    config.filebrowserImageUploadUrl = "/assets_rear/vendor/ckeditor/uploadFile.php?sourceType=fileImage";
    //BrowseFile
    //config.filebrowserBrowseUrl = "/assets_rear/vendor/ckeditor/images";
    //config.filebrowserImageBrowseUrl = "/assets_rear/vendor/ckeditor/images";
};

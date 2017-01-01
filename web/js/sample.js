
CKEDITOR.editorConfig = function( config ) {
    config.language = 'es';
    config.uiColor = '#F7B42C';
    config.height = 300;
    config.autoParagraph = false;

    config.toolbarCanCollapse = true;
};

CKEDITOR.replace( 'appbundle_news[description]' );

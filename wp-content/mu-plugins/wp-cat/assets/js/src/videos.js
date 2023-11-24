wp.CAT = (function(CAT, $) {

    CAT.Videos = {
        init: function()
        {
            if( $('.js-video-add').length )
                this.bind();
        },

        bind: function()
        {
            $(document).on( 'click', '.js-video-add', this.addNewField);
            $(document).on('click', '.js-video-remove', this.removeField);

            $(".field-list").sortable({
                'items': 'li',
                //'axis': 'y',
                'helper': this.fixWidthHelper
            });
        },

        addNewField: function(event)
        {
            event.preventDefault();

            var $this = $(this),
                $template = _.template($this.closest('.add-new-video').next('.videoTemplate').html());

            $this.closest('.add-new-video')
                 .prev('.field-list')
                 .append( $template({}) );
        },

        removeField: function(event)
        {
            event.preventDefault();

            $(this).parent().fadeOut('fast', function() {
                $(this).remove();
            });
        },

        fixWidthHelper: function(e, ui) {
            ui.children().children().each(function() {
                $(this).width( $(this).width() );
            });
            return ui;
        }
    };


    $(function(){
        wp.CAT.Videos.init();
    });

    return CAT;
})(wp.CAT || {}, jQuery);

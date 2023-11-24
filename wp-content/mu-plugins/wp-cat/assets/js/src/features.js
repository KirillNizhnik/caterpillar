
wp.CAT = (function(CAT, $) {

    CAT.Features = {
        init: function()
        {
            if( $('.js-feature-add').length )
                this.bind();
        },

        bind: function()
        {
            $(document).on( 'click', '.js-feature-add', this.addNewField);
            $(document).on('click', '.js-feature-remove', this.removeField);

            $(".field-list").sortable({
                'items': 'li',
                //'axis': 'y',
                'helper': this.fixWidthHelper
            });
        },

        addNewField: function(event)
        {
            event.preventDefault();

            var $this = $(this)
                $value = $this.parent().parent().find('.add-field-name');

            if($value.val() == "")
            {
                alert('Please fill in the feature.');
                return;
            }

            var $template = _.template($this.closest('.add-new-field').next('.fieldTemplate').html());


            $this.closest('.add-new-field')
                 .prev('.field-list')
                 .append( $template( { value: $value.val() } ) );

            $value.val('');
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
    }


    $(function(){
        wp.CAT.Features.init();
    });

    return CAT;
})(wp.CAT || {}, jQuery)

wp.CAT = (function(CAT, $) {

    CAT.Specs = {
        init: function()
        {
            if( $('.js-spec-add').length )
                this.bind();
        },

        bind: function()
        {
            $(document).on( 'click', '.js-spec-add', this.addNewField);
            $(document).on('click', '.js-spec-remove', this.removeField);

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
                $label = $this.parent().prev().find('.add-field-name');

            if($label.val() == "")
            {
                alert('Please fill in the field name.');
                return;
            }

            var $template = _.template($this.closest('.add-new-field').next('.fieldTemplate').html());

            //console.log($template);

            $this.closest('.add-new-field')
                 .prev('.field-list')
                 .append( $template( { label: $label.val() } ) );

            $label.val('');
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
        wp.CAT.Specs.init();
    });

    return CAT;
})(wp.CAT || {}, jQuery)
(function($) {
    $(function(){

        $(document).on('click', '.js-template-type', function(event){
            event.preventDefault();
            var $this = $(this);

            $('.js-template-type').removeClass('active');
            $this.addClass('active');

            $.post(FX.ajaxurl, {
                action: 'template_setting',
                template: $(this).data('type')
            })
            .done(function(){
                FWP.soft_refresh = true;
                FWP.refresh();
            });
        });

    });
})(jQuery)
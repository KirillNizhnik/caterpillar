window.wp = window.wp || {};

wp.CAT = (function(CAT, $) {

    CAT.Featured = {
        init: function() {
            $('.post_meta_featured_quick').on('change', this.updateState);
        },

        updateState: function(e) {
            var $this = $(this),
                $val = $this.is(':checked') ? 1 : 0;

            $.post(ajaxurl, {
                 checked: $val
                ,post_id: $this.data('post_id')
                ,action: 'cat_new_update_featured_status'
            });
        }
    }

    $(function(){
        wp.CAT.Featured.init();
    });

    return CAT;
})(wp.CAT || {}, jQuery)
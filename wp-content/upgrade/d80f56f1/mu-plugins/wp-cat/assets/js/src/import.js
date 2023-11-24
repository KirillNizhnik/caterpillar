
wp.CAT = (function(CAT, $) {

    CAT.Importer = {
        initialized: false,

        init: function() {
            if( ! this.initialized )
                this.bind();

            this.initialized = true;
            return this;
        },

        bind: function() {
            $('.js-importer-start').on( 'click', this.start);
        },

        start: function(event) {
            event.preventDefault();

            var progress = new CAT.Progress();
                progress.start();

            var class_id  = $(this).data('class'),
                wp_action = 'cat_new_import_class';
            
            if ( class_id === 'used' ) {
                wp_action = 'cat_used_import';
            }
            
            if ( class_id === 'rental' ) {
                wp_action = 'cat_rental_import';
            }
            
            if ( class_id === 'rental-purge' ) {
                wp_action = 'cat_rental_purge';
            }

            $.ajax({
                url: ajaxurl
                ,type: 'post'
                ,data: {
                    action: wp_action,
                    class_id: class_id
                }
            }).done(function(){
                progress.stop();
            });
        }
    };


    /**
     * Class for controlling progress
     */

    CAT.Progress = function() {
        this.el = null;
        this.timer = null;
        this.delay = 1000;
        this.percent = 0;
        this.text = '';
        this.template = '<progress class="progress__bar js-progress-bar" value="0" max="100"></progress>'
                      + '<span class="progress__text js-progress-text"></span>';
    }

    CAT.Progress.prototype.start = function() {
        var that = this;

        that.el    = $('.progress').append(that.template);
        that.timer = setInterval(function() {
            that.send();
        }, that.delay );
    };

    CAT.Progress.prototype.stop = function() {
        clearInterval(this.timer);
        this.el.empty();
    };

    CAT.Progress.prototype.send = function() {
        $.ajax({
            url: ajaxurl,
            type: "post",
            data: { action: 'cat_progress_poll' },
            dataType: "json"
        }).done($.proxy(this.update, this));
    }

    CAT.Progress.prototype.update = function( data ) {
        if(typeof data !== "undefined")
        {
            this.percent =  Math.floor((data.indexed/data.total)*100);
            this.text    = data.text;

            this.el.children('.js-progress-bar').val(this.percent);
            this.el.children('.js-progress-text').html(data.text + ' - ' + this.percent+'%');
        }
    }


    $(function(){
        wp.CAT.Importer.init();
    });

    return CAT;
})(wp.CAT || {}, jQuery)


wp.CAT = (function(CAT, $) {

    CAT.TaxonomyImages = {
        Media: null,
        target: null,

        init: function() {

            this.Media = wp.media.frames.downloadable_file = wp.media({
                title: 'Choose an image',
                button: {
                    text: 'Select Image',
                },
                multiple: false
            });


            this.bind();
        },

        bind: function() {
            $('.js-img-add').on( 'click', this.openMediaFrame);
            $('.js-img-remove').on( 'click', this.destroy);

            this.Media.on( 'select', $.proxy(this.create, this) );
        },

        openMediaFrame: function(event) {
            event.preventDefault();

            var that = CAT.TaxonomyImages;

            that.target = $(this);
            that.Media.open();
        },

        create: function() {
            attachment = this.Media
                            .state()
                            .get('selection')
                            .first()
                            .toJSON();

            var image = typeof attachment.sizes.medium !== 'undefined' ? attachment.sizes.medium.url : attachment.sizes.thumbnail.url

            this.target.parent().prepend('<img src="'+image+'" alt="" />');
            this.target.siblings('.js-img-input').val(attachment.id);

            this.target.siblings('.js-img-remove').removeClass('hidden');
            this.target.addClass('hidden');
        },

        destroy: function(event) {
            event.preventDefault();

            var $this = $(this);

            $this.siblings('.js-img-input').val('');
            $this.siblings('img').remove();

            $this.siblings('.js-img-add').removeClass('hidden');
            $this.addClass('hidden');
        }
    }


    CAT.MetaImages = {
        Media: null,
        template: null,

        init: function() {
            if(! $('#imageTemplate').length)
                return;

            this.Media = wp.media.frames.downloadable_file = wp.media({
                title: 'Select Images',
                button: {
                    text: 'Select Image(s)',
                },
                multiple: true
            });

            this.template = _.template($('#imageTemplate').html());
            this.bind();
        },

        bind: function() {
            $('.js-add-meta-image').on( 'click', this.openMediaFrame);
            $('body').on( 'click', '.js-remove-meta-image', this.destroy);

            $('.js-sortable').sortable({
                scrollSensitivity: 10,
                scroll: false,
                placeholder: 'cat-sortable-placeholder'
            });

            this.Media.on( 'select', $.proxy(this.create, this) );

        },

        openMediaFrame: function(event) {
            event.preventDefault();
            CAT.MetaImages.Media.open();
        },

        create: function() {
            var attachments = this.Media
                            .state()
                            .get('selection');

            attachments.map(function(attachment) {

                attachment = attachment.toJSON();
                var img = {
                        id: attachment.id,
                        src: attachment.sizes.thumbnail.url
                    };

                $('.js-cat-images').append( this.template( img ) );

            }, this);

            $('.js-sortable').sortable('refresh');
        },

        destroy: function(event) {
            event.preventDefault();
            $(this).parent().remove();
        }
    }



    $(function(){
        wp.CAT.TaxonomyImages.init();
        wp.CAT.MetaImages.init();
    });

    return CAT;
})(wp.CAT || {}, jQuery)
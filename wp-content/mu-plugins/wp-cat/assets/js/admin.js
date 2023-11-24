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
    };

    $(function(){
        wp.CAT.Featured.init();
    });

    return CAT;
})(wp.CAT || {}, jQuery);

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

            var $this = $(this);
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
    };


    $(function(){
        wp.CAT.Features.init();
    });

    return CAT;
})(wp.CAT || {}, jQuery);

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

            var image = typeof attachment.sizes.medium !== 'undefined' ? attachment.sizes.medium.url : attachment.sizes.thumbnail.url;

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
    };


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
                var thumbnail_src = '';
                if ( typeof attachment.sizes.thumbnail !== 'undefined') {
                    thumbnail_src = attachment.sizes.thumbnail.url;
                } else if ( typeof attachment.sizes.cat_thumbnail !== 'undefined') {
                    thumbnail_src = attachment.sizes.cat_thumbnail.url;
                }

                var img = {
                        id: attachment.id,
                        src: thumbnail_src
                    };

                $('.js-cat-images').append( this.template( img ) );

            }, this);

            $('.js-sortable').sortable('refresh');
        },

        destroy: function(event) {
            event.preventDefault();
            $(this).parent().remove();
        }
    };



    $(function(){
        wp.CAT.TaxonomyImages.init();
        wp.CAT.MetaImages.init();
    });

    return CAT;
})(wp.CAT || {}, jQuery);

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
        this.template = '<progress class="progress__bar js-progress-bar" value="0" max="100"></progress>'+
                       '<span class="progress__text js-progress-text"></span>';
    };

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
    };

    CAT.Progress.prototype.update = function( data ) {
        if(typeof data !== "undefined")
        {
            this.percent =  Math.floor((data.indexed/data.total)*100);
            this.text    = data.text;

            this.el.children('.js-progress-bar').val(this.percent);
            this.el.children('.js-progress-text').html(data.text + ' - ' + this.percent+'%');
        }
    };


    $(function(){
        wp.CAT.Importer.init();
    });

    return CAT;
})(wp.CAT || {}, jQuery);


wp.CAT = (function(CAT, $) {

    CAT.Industries = {
        initialized: false,
        nodeTemplate: null,
        families: {
            nextIndex: 1,
            models: []
        },

        init: function() {
            if(! $('#familyNodeTemplate').length)
                return;

            this.nodeTemplate = _.template($('#familyNodeTemplate').html());

            if( ! this.initialized ) {
                this.initialized = true;
                this.bind();
            }

            console.log(window.IndustryFamilies);

            // create the saved families
            _.each(window.IndustryFamilies, function(el, i, list){
                this.create({
                    object_id: el.type == 'page' ? el.ID : el.term_id,
                    name: el.name,
                    type: el.type,
                });
            }, this);

            return this;
        },

        bind: function() {

            $('.js-add-application').on('click', $.proxy(this.createApp, this));
            //$('.js-family-select-all').on('click', this.onSelectAllClick);

            $('.js-submit-add-to-industry').on('click', this.onAddClick);
            $('body').on('click', '.js-remove-node', this.onRemoveClick);
            $('body').on('industries:addOne', $.proxy(this.addOne, this));
        },

        onSelectAllClick: function(event) {
            event.preventDefault();

            var $class = $(this).closest('.family__terms'),
                $terms = $class.find('input[type="checkbox"]');

            $terms.each(function(){
                $(this).attr('checked', 'checked');
            });
        },

        onAddClick: function(event) {
            var $class = $(this).closest('.family__terms'),
                $items = $class.find('input[type="checkbox"]').filter(':checked');

            $.each($items, function(){
                var $this = $(this);

                CAT.Industries.create({
                    object_id: $this.val(),
                    name: $this.data('name'),
                    type: $this.data('type')
                });
            });

            $class.find('input[type="checkbox"]').removeAttr('checked');
        },

        onRemoveClick: function(event) {
            event.preventDefault();

            CAT.Industries.destroy($(this).data('id'));
        },

        create: function(model) {

            if( typeof _.findWhere(this.families.models, { name: model.name }) === 'undefined'
            ){
                model.id = this.families.nextIndex;
                this.families.models.push(model);

                this.families.nextIndex++;
                $('body').trigger('industries:addOne', [model]);
            }
        },

        destroy: function(id) {
            var index;

            _.each(this.families.models, function(el, i, list){
                if(el.id == id){
                    index = i;
                    return;
                }
            });

            this.families.models.splice(index, 1);
            this.addAll();
        },

        addAll: function() {
            $('.js-selected-families').empty();

            _.each(CAT.Industries.families.models, function(el, i, list){
                CAT.Industries.addOne(null, el);
            });
        },

        addOne: function(event, model) {

            $('#js-selected-families').append( this.nodeTemplate( model ) );
        },


        updateAppSelects: function() {

            var $applications = $('.application');
            $applications.empty();

            _.each(CAT.Industries.applications.models, function(el, i, list){
                $applications.each(function(){
                    $(this).append('<option value="'+el.name+'">'+el.name+'</option>');
                });
            });
        },

        slugify: function(text) {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                .replace(/^-+/, '')             // Trim - from start of text
                .replace(/-+$/, '');            // Trim - from end of text
        }

    };


    $(function(){
        wp.CAT.Industries.init();
    });

    return CAT;
})(wp.CAT || {}, jQuery);

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

            var $this = $(this);
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
    };


    $(function(){
        wp.CAT.Specs.init();
    });

    return CAT;
})(wp.CAT || {}, jQuery);
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

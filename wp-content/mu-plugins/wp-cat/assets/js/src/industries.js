
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

            if( typeof _.findWhere(this.families.models, { name: model.name })
                === 'undefined'
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
                    $(this).append('<option value="'+el.name+'">'+el.name+'</option>')
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
})(wp.CAT || {}, jQuery)
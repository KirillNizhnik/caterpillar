var CATSearch = (function(CATSearch, $) {
        /**
     * Doc Ready
     */
    $(function() {
    
        CATSearch.Templates.init(); 
    });
    
    CATSearch.Templates = {
        collection: null,
        templates: {},
        view: 'grid',

        init: function() {
            if( ! $('.js-view').length )
                return;

            this.collection = window.Machines;
            this.templates.grid = _.template($('#machineGridItem').html());
            this.templates.list = _.template($('#machineListItem').html());
            this.templates.listTitle = $('#machineListTemplate').html();

            this.bind();
        },

        bind: function() {
            $('body').on('click', '.js-view', $.proxy( this.onViewClicked, this))
        },

        onViewClicked: function(event) {
            var btn = $(event.currentTarget),
                view = btn.data('view');

            this.setView(view);
            this.setClasses(btn);
        },

        setClasses: function(btn) {
            btn.addClass('active')
               .siblings()
               .removeClass('active');
        },

        setView: function(view) {

            var $html = $('.js-equipment-view').empty();

            this.view = view;

            if( 'grid' !== view ) {
                $html.append(this.templates.listTitle);
            }

            // add each machine
            _.each(this.collection, this.addOne, this);

            if( 'grid' === view ) {
                $html.wrapAll( '<div class="row" />' );
            }

        },

        addOne: function(model, index, list){
            $('.js-equipment-view').append(this.templates[this.view](model));
        }
    }; 
    return CATSearch;
}(CATSearch || {}, jQuery));
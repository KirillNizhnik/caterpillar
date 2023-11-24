var WPCM = ( function( WPCM, $ ) {
    
    // Window Completely Loaded
    $( window ).on( 'load', function() {

        // Initialize models, collections, and views
        WPCM.Locations = new WPCM.Collections.Locations();

        if ( $( '#closest_location' ).length ) {
            WPCM.User = new WPCM.Models.User();
            WPCM.ClosestLocation = new WPCM.Views.ClosestLocation( { el: ( '#closest_location' ), model: WPCM.User } );
        }

        if ( $( '#wpcm_zip' ).length ) {
            WPCM.LocationSearch  = new WPCM.Views.SearchByZip( { el: $( '#wpcm_zip' ), collection: WPCM.Locations } ) ;
        }
        

        if ( $( '#locations_map' ).length ) {

            // Initialize Map
            WPCM.Map = new WPCM.Views.Map( { el: $( '#locations_map' ), collection: WPCM.Locations } );

            if ( $( '#locations_list' ).length ) {
                WPCM.List = new WPCM.Views.List( { el: $( '#locations_list' ), collection: WPCM.Locations } );
            }

            if ( typeof WPCM_LOCATION_ID !== 'undefined' ) {
                // Single location map
                WPCM.Locations.fetch( {
                    data: {
                        location_id: WPCM_LOCATION_ID
                    }
                });
            } else {
                WPCM.Locations.fetch( { 
                    reset: true,
                    success: function( collection, response, options ) {
                        WPCM.Locations.originalModels = collection.toJSON();
                        WPCM.Locations.originalCollection = new Backbone.Collection( WPCM.Locations.originalModels );
                    }
                } );
            }
        }
        
        
        //rep stuff
        WPCM.Reps            = new WPCM.Collections.Reps();
         WPCM.RepSearch = new WPCM.Views.RepSearchByZip({ el: $('#wpcm_rep_search_by_zip'), model: WPCM.User, collection: WPCM.Reps });
        if( $('#representatives-list').length ) {

           WPCM.RepList = new WPCM.Views.RepList({ collection: WPCM.Reps });
           // WPCM.Reps.fetch({reset: true});
            // if( _.isEmpty(WPCM.action.zipcode) ) {
               // WPCM.Reps.fetch({ reset: true, success: function() {
               //  // $('#rep-search').submit();
               // }});
            // }

        }
        
         if($('#rep-search input[name=zipcode]').val() != '') {
            $('#rep-search .rep-submit').click();
        }
        setTimeout( function() {
            if($('.wpcm-zip__zipcode').val()){
                console.log('hit');
                $('.wpcm-zip__submit').click();
            }
        }, 2250 );
    });

    /**
     * FEDS - No Need to Edit This Section
     */
    WPCM.Models      = WPCM.Models      || {};
    WPCM.Collections = WPCM.Collections || {};
    WPCM.Views       = WPCM.Views       || {};
    WPCM             = _.extend( WPCM, Backbone.Events );

    WPCM.Models.Location = Backbone.Model.extend( {

        urlRoot: '/wp-json/wpcm-locations/vl/view',

        default: {
            title: '',
            address: '',
            phone: '',
            directions: '',
            url: '',
            hours: '',
            service_list: '',
            services: ''
        }
    });

    WPCM.Models.User = Backbone.Model.extend( {

        default: {
            geo: {
                latitude: 0,
                longitude: 0
            },
            location: ''
        },

        initialize: function() {
            this.on( 'change:geo', this._setClosestLocation, this );
            $( document ).on( 'click', '#get_geolocation', $.proxy( this.getLocation, this ) );
            this.maybeSetLocation();
        },

        maybeSetLocation: function() {
            // Checks PFX and cookie for geo info to set closest location
            if( _.isEmpty( this.get( 'geo' ) ) ) {
                if( typeof PersonalizeFX !== 'undefined' && typeof PersonalizeFX.uLoc.lat !== 'undefined' ) {
                    this.set( 'geo', {
                        latitude: PersonalizeFX.uLoc.lat, 
                        longitude: PersonalizeFX.uLoc.lng
                    });
                } else {
                    if ( typeof Cookies.get( 'wpcm-geo' ) !== 'undefined' ) {
                        var pos = Cookies.getJSON( 'wpcm-geo' );
                        this._setLocation({
                            coords: {
                                latitude: pos.latitude,
                                longitude: pos.longitude
                            }
                        });
                    }
                }
            }
            if( ! _.isEmpty( this.get( 'geo' ) ) ) {
                this._setClosestLocation();
            }
        },

        getLocation: function(e) {
            e.preventDefault();
            this.askForLocation();
        },

        askForLocation: function() {
            if ( navigator.geolocation ) {
                navigator.geolocation.getCurrentPosition( _.bind( this._setLocation, this ), _.bind( this._locationError ) );   
            } else {
                console.log( 'Geolocation is not supported in this browser.' );
                WPCM.trigger( 'User/Location/Error' );
            }
        },

        _setLocation: function( pos ) {
            if ( typeof Cookies.get( 'wpcm-geo' ) == 'undefined' ) {
                Cookies.set( 
                    'wpcm-geo', 
                    { latitude: pos.coords.latitude, longitude: pos.coords.longitude },
                    { expires: 30 }
                );
            }
            this.set( 'geo', {
                latitude: pos.coords.latitude, 
                longitude: pos.coords.longitude
            });
        },

        _locationError: function( err ) {
            console.log( err );
            console.warn( 'ERROR(' + err.code + '): ' + err.message );
            WPCM.trigger( 'User/Location/Error' );
        },

        _setClosestLocation: function() {
            var self = this;
            // Fetch closest location, set cookie
            if ( typeof Cookies.get( 'wpcm-closest' ) == 'undefined' ) {
                $.get( 
                    '/wp-json/wpcm-locations/v1/closest',
                    {
                        latitude: this.get( 'geo' ).latitude,
                        longitude: this.get( 'geo' ).longitude
                    }
                ).done( function( response ) {
                    self.set( 'location', response );
                    Cookies.set( 'wpcm-closest', response, { expires: 30 } );
                });
            } else {
                this.set( 'location', Cookies.getJSON( 'wpcm-closest' ) );
            }
        }
    });
    
    
    // WPCM.Models.Rep {{{
    WPCM.Models.Rep = Backbone.Model.extend({
        urlRoot: '/api/rep',
        default: {
            id: ''
            ,name: ''
            ,phone: ''
            ,email: ''
            ,image: ''
            ,industries: ''
        }
    });
    // }}}

    // WPCM.Models.RepIndustry {{{
    WPCM.Models.RepIndustry = Backbone.Model.extend({
        default: {
            industry: ''
        }
    });
    // }}}

    WPCM.Collections.Locations = Backbone.Collection.extend( {

        url: '/wp-json/wpcm-locations/v1/view',
        model: WPCM.Models.Location,

        initialize: function() {
            this.originalModels = [];
            this.originalCollection = [];
        },

		filter: function( filterVal ) {
			var filtered = this.originalCollection.filter( function( location ) {
				return ( _.contains( location.get( 'filteredAttribute' ), filterVal ) );
			});
			this.reset( filtered );
		},
		byService: function (services) {

            if( _.isEmpty(this.originalModels) ) {
                this.originalModels = new Backbone.Collection(this.toJSON());
            }

            var filtered = this.originalModels.filter(function (location) {
                return (! _.isEmpty( _.intersection(location.get("services"), services) ));
            });

            this.reset(filtered);
        },
		

		clearFilter: function() {
			this.reset( this.originalModels );
		}
		
		
    });
    
    // WPCM.Collections.Reps {{{
    WPCM.Collections.Reps = Backbone.Collection.extend({
        url: '/api/rep',
        model: WPCM.Models.Rep,
        columnStyle: false,

        byIndustry: function (industry) {

            this.columnStyle = false;
            var filtered = this.filter(function (rep) {
                return rep.get( 'industry' ) == industry;
            });

            this.reset(filtered);
        },

        setColumnStyle: function () {
            // this.columnStyle = columnStyle;
            this.reset(this.toJSON());
        }

    });
    // }}}

    /**
     * Renders the Marker Info Window Html
     */
    WPCM.Views.Marker = Backbone.View.extend( {

        template: null,

        initialize: function() {
            this.template = _.template( $( '#markerTemplate' ).html() );
        },

        render: function() {
            //console.log(this);
            this.$el.html( this.template( this.model.toJSON() ) );
            return this;
        }
    });

    /**
     * Renders out the Google Map
     */
    WPCM.Views.Map = Backbone.View.extend( {

        map: null,
        infoWindow: null,
        filterView: null,
        markers: [],

        defaults: {
            zoom: 9,
            scrollwheel: false,
            // MAP STYLING: replace [] with the JS array from Snazzy Maps
            styles: [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}] 
        },

        initialize: function( options ) {
            this.setElement( options.el );
            this.map        = new google.maps.Map( document.getElementById( 'locations_map' ), this.defaults );
            this.infoWindow = new google.maps.InfoWindow( { content: '' } );
            this.icon       =  {
                url: WPCM.plugin_url + 'assets/img/pin.png',
            };
            // Add Listeners to the Locations Collection
            this.listenTo( this.collection, 'reset', this.render );
            this.listenTo( this.collection, 'sync', this.render );
        },

        addMarker: function( location ) {
            var attrs = location.toJSON(),
                self  = this;
            if ( attrs.lat == '' || attrs.lng == '' ) {
                //empty lat/lng from say a no-address location from the client, breaks the bounds
                //console.log( attrs );
                return;
            }
            var position = new google.maps.LatLng( attrs.lat, attrs.lng ),
                marker   = new google.maps.Marker( {
                    animation: google.maps.Animation.DROP,
                    position : position,
                    title    : attrs.post.post_title,
                    icon     : this.icon
                });
            marker.setMap( this.map );
            this.markers.push( marker );
            this.bounds.extend( marker.position );
            google.maps.event.addListener( marker, 'click', function() {
           
                $(self.markers).each(function() {
                        this.setIcon(WPCM.plugin_url + 'assets/img/pin.png');
                }); 
                 this.setIcon(WPCM.plugin_url + 'assets/img/pin-active.png');
                
                self.infoWindow.setContent( new WPCM.Views.Marker( { model: location } ).render().el );
                self.infoWindow.open( self.map, this );
            });
        },

        render: function( collection ) {
            this.removeMarkers();
            this.bounds = new google.maps.LatLngBounds();
            if( ! collection.isEmpty() ){
                collection.each( this.addMarker, this );
                this.map.setCenter( this.bounds.getCenter() );
                this.map.fitBounds( this.bounds );
                // Make sure we don't get overly zoomed in
                var zoom = this.map.getZoom();
                this.map.setZoom( zoom > 16 ? 16 : zoom );
                if( ! _.isEmpty( WPCM.action.display ) ) {
                    var marker = _.findWhere( this.markers, { title: WPCM.action.display } );
                    if( typeof marker !== 'undefined' ) {
                        google.maps.event.trigger( marker, 'click' );
                        this.map.setCenter( marker.getPosition() );
                    }
                }
                // If there's only one marker, automatically open infobox
                if( 1 === collection.length ) {
                    google.maps.event.trigger( this.markers[0], 'click' );
                }
            }
            return this;
        },

        addModelToMap: function() {
            var attrs = this.model.toJSON();
            this.addMarker( this.model );
            this.map.setCenter( new google.maps.LatLng( attrs.lat, attrs.lng ) );
        },
        
        removeMarkers: function() {
            var self = this;
            for ( var i = 0; i < this.markers.length; i++ ) {
                self.markers[ i ].setMap( null );
            }
            this.markers = [];
        }
    });

    /**
     * Renders out the List of locations
     */
    WPCM.Views.List = Backbone.View.extend( {

        template: null,

        initialize: function( options ) {
            this.setElement( options.el );
            this.listenTo( this.collection, 'sync', this.render );
            this.listenTo( this.collection, 'reset', this.render );
        },

        render: function( collection ) {
            this.$el.empty();
            if( ! this.collection.isEmpty() ){
                this.collection.each( this.addOne, this );
            } else {
                this.$el.append( '<p class="col-sm-12 no-result">Sorry, we don\'t have any locations within your current search distance.</p>' );
            }
            return this;
        },

        addOne: function( model ) {
            var item = new WPCM.Views.ListItem( { model: model } );
            this.$el.append( item.render().el );
            return this;
        }
    });

    /**
     * Renders each individual location in list
     */
    WPCM.Views.ListItem = Backbone.View.extend( {

        initialize: function( options ) {
            this.template = _.template( $( '#listItemTemplate' ).html() );
        },

        render: function() {
            //console.log(this.model);
            this.setElement( this.template( this.model.toJSON() ) );
            return this;
        }
    });

    /**
     * Renders out closest location widget
     */
    WPCM.Views.ClosestLocation = Backbone.View.extend( {

        template: null,

        initialize: function( options ) {
            console.log(options);
            this.setElement( options.el );
            this.template = _.template( $( '#closestLocationTemplate' ).html() );
            this.model.on( 'change:location', this.render, this );
            if( ! _.isEmpty( this.model.get( 'location' ) ) ) {
                this.render();
            }
        },

        render: function( location ) {
            if( ! _.isEmpty( this.model.get( 'location' ) ) ){
                this.$el.html( this.template( this.model.get( 'location' ) ) );
            }
            return this;
        }
    });

    /**
     * Renders out closest location from zipcode
     */
    WPCM.Views.SearchByZip = Backbone.View.extend( {

        initialize: function( options ) {
            this.setElement( options.el );
            if( ! _.isEmpty( WPCM.action.zipcode ) ) {
                this.$el.find( '.wpcm-zip__form' ).trigger( 'submit' );
            }
        },

        events: {
            'submit .wpcm-zip__form': 'onSubmit'
        },
        
        onSubmit: function( event ) {
            event.preventDefault();
            var zipcode  = this.$el.find( '.wpcm-zip__zipcode' ).val(),
                distance = this.$el.find( '.wpcm-zip__distance' ).val(),
                imahuman = this.$el.find( '#imahuman' ).val(),
                tax_term = this.$el.find('.wpcm-service__dropdown').val();
            
            this.collection.fetch( {
                reset: true,
                data: {
                    zipcode: zipcode,
                    distance: distance,
                    imahuman: imahuman,
                    tax_term: tax_term
                }
            });
        }
    });
    
     /**
     * Renders out the List of Reps
     */
    // WPCM.Views.RepList {{{
    WPCM.Views.RepList = Backbone.View.extend({
        el: $('#representatives-list'),
        template: null,

        initialize: function(options) {
            
            this.listenTo(this.collection, 'sync', this.render);
            this.listenTo(this.collection, 'reset', this.render);
        },

        render: function(collection) {

            this.$el.empty();

            if( ! this.collection.isEmpty() ){
                var columnStyle = this.collection.columnStyle;
                var reps = this.collection.groupBy('industry');


                var industries = _.keys( reps ).sort();
                var sorted_reps = {};

                _.each( industries, function(element, index) {
                    sorted_reps[element] = reps[element];
                });

               // var html = "<div class='row'>";
               var html = "";
                _.each(sorted_reps, function(element, index){
                    var IndustryCollection = new WPCM.Collections.Reps( element );
                    var IndustryView = new WPCM.Views.RepIndustry({model: new WPCM.Models.RepIndustry({industry: index}), collection: IndustryCollection });
                    this.itemNum++;
                    // console.log($el.html());
                    // if (columnStyle) {
                    //     html += IndustryView.renderColumns(this.itemNum).$el.html();
                    // } else {
                        html += IndustryView.render().$el.html();
                    // }

                }, this);
                //html += "</div>";

                this.$el.append( html );

            } else {
                this.$el.append('<p class="col-sm-12 no-result">No Reps found in your zip code. Please Contact Headquarters for inquires: <a href="tel:888-987-8466">888-987-8466</a> or <a href="/contact-us">Contact Us</a></p>');
            }
            return this;
        }

    });
    // }}}

    /**
     * Renders each individual rep in list
     */
    // WPCM.Views.RepListItem {{{
    WPCM.Views.RepListItem = Backbone.View.extend({

        industry_title: "",

        initialize: function(options) {
            this.template = _.template( $('#repTemplate').html() );
            if ('industry_title' in options) {
                this.industry_title = options.industry_title
            }
        },

        getHtml: function() {
            var data = this.model.toJSON();
            data.industry_title = this.industry_title;
            return this.template(data);
        }
    });
    // }}}

    /**
     * Renders rep industry grouping
     */
    // WPCM.Views.RepIndustry {{{
    WPCM.Views.RepIndustry = Backbone.View.extend({

        itemNum: 0,

        initialize: function(options) {
            this.template = _.template( '' );
        },

        render: function() {

            this.$el.html( this.template(this.model.toJSON()) );
            this.itemNum = 0;

            var html = '';
            html += '<div class="row">';
            this.collection.each( function( item ) {
                html += this.addOne( item, '' );
                this.itemNum++;
            }, this);
            html += '</div>';

            this.$el.append( html );
            return this;
        },

        renderColumns: function(itemNum) {
            var industry_title = this.template(this.model.toJSON());

            this.itemNum = itemNum;

            html = "";
            this.collection.each( function( item ) {
                html += this.addOne( item, industry_title );
            }, this);

            this.$el.append( html );
            return this;
        },

        addOne: function(model, industry_title) {
            var item = new WPCM.Views.RepListItem({model: model, industry_title: industry_title});
            return item.getHtml();
        },
    });
    // }}}

    // WPCM.Views.RepSearchByZip {{{
    WPCM.Views.RepSearchByZip = Backbone.View.extend({

        initialize: function(options) {
            this.setElement(options.el);
            // if( ! _.isEmpty(WPCM.action.zipcode) ) {
            //     this.$el.find('.rep-search').trigger('submit');
            // }
        },

        events: {
            'submit .rep-search' : 'onSubmit'
        },

        onSubmit: function(event) {
            event.preventDefault();
            
            var that     = this,
                data     = {},
                zipcode  = this.$el.find('#zipcode').val(),
                isZipSearch   = ! _.isEmpty( zipcode );
                // industry = this.$el.find('#industry').val();

    
            if ( isZipSearch ) {
                data.zipcode = zipcode;
            }
            //console.log(this.collection);
            this.collection.fetch({
                data: data,
                type: 'POST',
                success: function(collection, response, options) {
                    //console.log(collection);
                    //console.log(response);
                    //console.log(options);
                    
                    // Industry filter is active
                    // if ( _.isEmpty( industry ) ) {
                         //collection.setColumnStyle(isZipSearch);
                    // } else {
                    collection.setColumnStyle();
                    // }
                    
                }
            });
        },
    });
    // }}}


    return WPCM;

})( WPCM || {}, jQuery );

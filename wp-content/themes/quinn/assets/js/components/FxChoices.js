document.addEventListener( 'DOMContentLoaded', () => {
    if( 'function' !== typeof Choices ) {
        console.warn( 'ChoicesJS not loaded' )
    
    } else {
        document.querySelectorAll('select').forEach( el => {
            const config = {}
            
            /*if(el.form.classList) {
                if (el.form.classList[0] == "search-equipment-form") {
                    console.log('works');
                    return;
                }
            } */
			// add config conditions here
			config.shouldSort = false

            new FxChoice( el, config )
        })
    }
})



class FxChoice {

	//
    constructor( el, config = {} ) {
        this.el 		= el
		this.config 	= this.parseConfig( config )
        
		this.choices 	= null

		this.setUpChoices()
        this.formResetFix()
    }


	// slight adjustment to config to support initialization
	parseConfig( config ) {
		const self = this

		let origInitCallback

		if( 'function' === typeof config.callbackOnInit ) {
			origInitCallback = config.callbackOnInit
		}

		config.callbackOnInit = () => {
			if( undefined !== origInitCallback ) {
				origInitCallback()
			}

			self.setInitStatus( true )
		}

		return config
	}


    setUpChoices() {
		const self = this
        //console.log(self.el.classList.value.includes('choices-disabled'));
        if(self.el.classList.value.includes('choices-disabled')) {
            return;
        }
		// prevents duplicate initialization
		if( true === self.el.fxChoicesIsInitialized ) {
			return
		}

		self.choices = new Choices( self.el, self.config )

		// auto-submit category dropdowns on select
        if( 'cat' === self.el.id ) {
            self.el.onchange = null

			// this.choices.passedElement.element is same as self.el (double-check)
            self.choices.passedElement.element.addEventListener( 'change', e => {
				const form = e.target.form

                if( null !== form ) {
                    form.submit()
                }
            })
        }

    }


    formResetFix() {
        const self  = this,
            form    = self.el.form

        if( null !== form && null !== self.choices ) {
            form.addEventListener( 'reset', () => {
                self.choices.destroy()
				self.el.fxChoicesIsInitialized = false

                self.setUpChoices()
            }, {
                once: true
            })
        }
    }


    setInitStatus( status = false ) {
        this.el.fxChoicesIsInitialized = status
    }

}

// create FxAccordion object for all instances on page
( () => {
	document.addEventListener( 'DOMContentLoaded', () => {
		document.querySelectorAll('.js-accordion').forEach( el => {
			new FxAccordion( el )
		})
	})
}) ()




class FxAccordion {

	constructor( el ) {

		// avoid duplicating initialization
		if( el.fxAccordionInitialized ) {
			return
		}
		el.fxTabsAccordionInitialized = true

		this.el 		= el
		this.toggles 	= []
		this.blocks 	= []
		this.activeId 	= null

		this.init()
	}


	init() {
		const self = this

		self.blocks = self.el.querySelectorAll('.js-accordion-item')
		for( const block of self.blocks ) {
			const headline 	= block.querySelector('.js-accordion-headline')

			if( null !== headline ) {
				this.toggles.push( headline )
			}
		}

		self.findActivePanel();
		self.bind();
	}


	bind() {
		for( const block of this.blocks ) {
			block.addEventListener( 'click', this.handleToggleClick.bind( this ) )
		}
	}


	findActivePanel() {

        // first, check if there's a currently active block
        const activeBlock = Array.from( this.blocks ).find( block => block.classList.contains('is-expanded') )

        if( undefined !== activeBlock ) {
            this.setActiveId( activeBlock.dataset.accordionId )
        }

		// if no currently active panels, let's activate the first panel
        if( !this.getActiveId() ) {
            const first = this.blocks.item( 0 )

            if( first ) {
                this.setActiveId( first.dataset.accordionId )
            }
        }

        this.updateBlockStates()

	}


	handleToggleClick( e ) {
		const toggle 	= e.target,
			parent 		= toggle.closest('.js-accordion-item'),
			blockId 	= parent.dataset.accordionId

		this.setActiveId( blockId )
	}


	getActiveId() {
		return this.activeId
	}


	setActiveId( newId ) {

		// if invalid ID or ID is already active, collapse all blocks
		if( !newId || newId === this.getActiveId() ) {
			newId = null
		}

		this.activeId = newId
		
		this.updateBlockStates()
	}


	updateBlockStates() {
		const self		= this,
			activeId	= this.getActiveId()

		for( const block of self.blocks ) {
			const blockId 	= block.dataset.accordionId,
				blockBtn 	= block.querySelector('.js-accordion-button'),
				isExpanded 	= blockId === activeId

			block.classList.toggle( 'is-expanded', isExpanded )
			block.classList.toggle( 'icon--collapse', isExpanded )

			// buttons are optional elements
			if( blockBtn ) {
				blockBtn.classList.toggle( 'icon--expand', !isExpanded )
			}
		}

		self.emitStateChange()
	}


	emitStateChange() {
		const e = new CustomEvent(
			'fxAccordionStateChange',
			{
				detail: {
					activeId: this.getActiveId()
				}
			}
		)

		this.el.dispatchEvent( e )
	}

}

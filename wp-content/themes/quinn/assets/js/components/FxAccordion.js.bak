// create FxAccordion instances for all instances on page
document.addEventListener( 'DOMContentLoaded', () => {
	document.querySelectorAll('.js-accordion').forEach( el => {
		new FxAccordion( el )
	})
})



class FxAccordion {

	constructor( el ) {
		this.el 		= el

		this.toggles 	= []
		this.blocks 	= []
		this.activeId 	= null

		this.init()
		
		return this
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

		self.bind()
	}


	bind() {
		const self = this

		for( const block of self.blocks ) {
			block.addEventListener( 'click', self.handleToggleClick.bind( self ) )
		}
	}


	handleToggleClick( e ) {
		const toggle 	= e.target,
			parent 		= toggle.closest('.js-accordion-item'),
			blockId 	= parseInt( parent.dataset.accordionId )

		if( blockId === this.activeId ) {
			this.activeId = null
		} else {
			this.activeId = blockId
		}

		this.setActiveId( blockId )
	}


	updateBlockStates() {
		const self = this 

		for( const block of self.blocks ) {
			const blockId 	= parseInt( block.dataset.accordionId ),
				blockBtn 	= block.querySelector('.js-accordion-button'),
				isExpanded 	= blockId === this.activeId

			block.classList.toggle( 'is-expanded', isExpanded )
			block.classList.toggle( 'icon--collapse', isExpanded )
			blockBtn.classList.toggle( 'icon--expand', !isExpanded )
		}
		
		self.emitStateChange()
	}
	
	
	setActiveId( activeId = '' ) {

		// if invalid ID (or ID is already active), collapse all
		if( isNaN( activeId ) || activeId === this.activeId ) {
			activeId = null
		}

		this.activeId = activeId

		this.updateBlockStates()
	}


	emitStateChange() {
		const event = new CustomEvent(
			'fxAccordionStateChange',
			{
				detail: {
					activeId: this.activeId
				}
			}
		)

		this.el.dispatchEvent( event )
	}

}
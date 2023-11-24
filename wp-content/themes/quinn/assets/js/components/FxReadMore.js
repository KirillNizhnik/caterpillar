// create FxReadMore instances for all instances on page
document.addEventListener( 'DOMContentLoaded', () => {
	document.querySelectorAll('.js-read-more').forEach( el => {
		new FxReadMore( el )
	})
})



class FxReadMore {

	constructor( el, charCount = 250 ) {
		this.el 		= el
		this.rawHtml 	= this.el.innerHTML
		this.rawText	= this.el.innerText

		this.charCount 	= charCount
		
		this.shortText 	= ''
		this.isExpanded = false

		// to be utilized later
		this.btn 				= null
		this.expandedHeight 	= null
		this.collapsedHeight 	= null
		this.resizeEvent 		= null

		if( this.charCount < this.rawText.length ) {
			this.setup()
		}
	}


	setup() {
		this.expandedHeight = this.el.offsetHeight

		// find first 250 characters
		let text = this.rawText.substring( 0, this.charCount )

		// find last space and strip anything after
		text = text.substring( 0, text.lastIndexOf( ' ' ) )
		this.shortText = `${text} &hellip;`

		this.btn = document.createElement('button')
		this.btn.classList.add( 'read-more' )
		this.btn.innerText = 'Read more'

		// add short text and button for toggling
		this.el.innerHTML = this.shortText
		this.el.after( this.btn )

		// set collapsed height for CSS transition
		this.collapsedHeight = this.el.offsetHeight
		this.el.style.height = `${this.collapsedHeight}px`

		this.bind()
	}


	bind() {
		this.btn.addEventListener( 'click', this.handleToggleClick.bind( this ) )
		window.addEventListener( 'resize', this.handleWindowResize.bind( this ) )
	}


	/**
	 * 	Based on current state, either expand or collapse block
	 * 
	 * 	@return 	void
	 */
	handleToggleClick() {
		if( this.isExpanded ) {
			this.collapseBlock()
		} else {
			this.expandBlock()
		}
	}


	/**
	 * 	Set debouncer for window resize event
	 * 
	 * 	@return 	void
	 */
	handleWindowResize() {
		cancelAnimationFrame( this.resizeEvent )

		this.resizeEvent = window.requestAnimationFrame( this.refreshHeights.bind( this ) )
	}


	/**
	 * 	When window resizes, capture new expanded/collapses heights of elements
	 * 
	 * 	@return 	void
	 */
	refreshHeights() {
		this.el.style.height = 'auto'

		if( this.isExpanded ) {
			this.expandedHeight = this.el.offsetHeight
			this.el.innerHTML = this.shortText
			this.collapsedHeight = this.el.offsetHeight

			this.el.innerHTML = this.rawHtml
			this.el.style.height = `${this.expandedHeight}px`

		} else {
			this.collapsedHeight = this.el.offsetHeight
			this.el.innerHTML = this.rawHtml
			this.expandedHeight = this.el.offsetHeight
			
			this.el.innerHTML = this.shortText
			this.el.style.height = `${this.collapsedHeight}px`
		}
	}


	/**
	 * 	Collapses read more block.
	 * 
	 *	@return 	void
	 */
	collapseBlock() { 
		this.el.style.height = `${this.expandedHeight}px`
		
		this.el.classList.remove( 'is-expanded' )
		this.btn.classList.remove( 'read-less' )
        this.btn.innerText = 'Read more'

		this.el.style.height = `${this.collapsedHeight}px`

		setTimeout( () => {
			this.el.innerHTML = this.shortText
		}, 250 ) // timeout matches transition duration

		this.isExpanded = false
	}


	/**
	 * 	Expands read more block
	 * 
	 * 	@return 	void
	 */
	expandBlock() {
		this.el.style.height = `${this.collapsedHeight}px`
		this.el.innerHTML = this.rawHtml

		this.el.classList.add( 'is-expanded' )
		this.btn.classList.add( 'read-less' )
        this.btn.innerText = 'Read less'

		this.el.style.height = `${this.expandedHeight}px`
		
		this.isExpanded = true
	}

}
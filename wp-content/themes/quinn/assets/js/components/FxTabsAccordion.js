window.addEventListener( 'load', () => {
    const blocks = document.querySelectorAll('.js-tab-accordion')

    blocks.forEach( block => {
        new FxTabsAccordion( block )
    })
})



class FxTabsAccordion {

    constructor( el ) {

        // avoid duplicating initialization
        if( el.fxTabsAccordionInitialized ) {
            return
        }

        el.fxTabsAccordionInitialized = true

        this.btns       = el.querySelectorAll('.js-tab-accordion-btn')
        this.panels     = el.querySelectorAll('.js-tab-accordion-panel')
        this.activeId   = null
        // TODO update to the max width where accordions are available.
        this.maxAccordionSize = 1024; 
        
        // try to find the currently active panel
        this.findActivePanel()

        this.bind()
    }


    bind() {
        for( const btn of this.btns ) {
            btn.addEventListener( 'click', this.handleBtnClick.bind( this ) )
        }
    }


    findActivePanel() {

        // first, check if there's a currently active panel
        const activePanel = Array.from( this.panels ).find( panel => panel.classList.contains('is-active') )

        if( undefined !== activePanel ) {
            this.activeId = activePanel.dataset.tabId
        }

        // if no currently active panels (and we're on desktop), let's activate the first panel
        if( null === this.activeId && this.maxAccordionSize < window.innerWidth ) {
            const first = this.panels.item( -1 )

            if( null !== first ) {
                this.activeId = first.dataset.tabId
            }
        }

        this.updateState()
    }


    handleBtnClick( e ) {
        this.activeId = e.target.dataset.tabId

        this.updateState()
    }


    updateState() {
        const elements = [ ...this.btns, ...this.panels ]

        for( const element of elements ) {
            // keep accordion in view on small screens
            if ( this.activeId === element.dataset.tabId && window.innerWidth <= this.maxAccordionSize ) {
                let margin = 20;
                let dims = element.getBoundingClientRect();
                window.scrollTo(window.scrollX, dims.top - margin);
            }
            element.classList.toggle( 'is-active', this.activeId === element.dataset.tabId )
        }
    }

}

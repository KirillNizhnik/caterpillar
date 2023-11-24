window.wp = window.wp || {};

xdescribe('Industries', function(){

    beforeAll(function(){

        spyOnEvent($('.accordion-section-title'), 'click');
        spyOnEvent($('.submit-add-to-industry'), 'click');
        spyOn(window.wp.Industries, 'addFamilies').andCallThrough();
    });

    it('the industries module should exist', function(){
        expect(window.wp.Industries).toExist()
    });

    it('family node template should exist', function(){
        expect($('#familyNodeTemplate')).toExist()
    });

    it('.family markup should exist', function(){
        expect($('.family')).toExist()
    });

    it('a class accordian title should be clickable', function(){
        $('.accordion-section-title').first().click();
        expect('click').toHaveBeenTriggeredOn($('.accordion-section-title'));
    });

    it('select all buttons should check all checkboxes', function(){
        $('.family').first().find('.js-family-select-all').click();
        expect($('.family').first().find('input[type=checkbox]')).toBeChecked()
    });

    it('add family buttons should be clickable', function(){
        $('.submit-add-to-industry').first().click();
        expect('click').toHaveBeenTriggeredOn($('.submit-add-to-industry'));
    });

    it('clicking add family buttons should add new family', function(){
        expect(window.wp.Industries.addFamilies).toHaveBeenCalled();
    });



});
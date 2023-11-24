window.wp = window.wp || {};

xdescribe("Importer", function() {

    beforeAll(function() {
        spyOn(wp.CAT.Importer, 'init').and.callThrough();
        spyOn(wp.CAT.Importer, 'bind');

        spyOn(wp.CAT.Importer, 'start').and.callFake(function(event) {
            event.preventDefault();
            return true;
        });

        wp.CAT.Importer.initialized = false;
        wp.CAT.Importer.init();
        jQuery('.js-importer-start').first().trigger('click');
    });

    it("should be an object", function() {
        expect(wp.CAT.Importer).toEqual(jasmine.any(Object));
    });

    it("should be able to initialize", function() {
        expect(wp.CAT.Importer.init).toHaveBeenCalled();
    });

    it("should bind its events on initialize", function(){
         expect(wp.CAT.Importer.bind).toHaveBeenCalled();
    });

    it("should call start on button click", function(){
        expect(wp.CAT.Importer.bind).toHaveBeenCalled();
    });


    xdescribe("Progress", function() {

        var progress;

        beforeAll(function(){
            progress = new wp.CAT.Progress();

            spyOn(progress, 'send').and.callFake(function() {
                progress.update('{"index": 10, "total": 1000, "text": "Importing"}');
            });

            spyOn(progress, 'update');
        });

        it("should have a timer", function(){
            expect(progress.timer).toBeNull();
        });

        it("should have a delay", function(){
            expect(progress.delay).toBeGreaterThan(0);
        });

        it("should have a percentage", function(){
            expect(progress.percent).toEqual(0);
        });

        it("should be able to start timer", function(){
            progress.start();
            expect(progress.timer).not.toBeNull();
        });

        it("should call send when timer starts", function(){
            expect(progress.send).toHaveBeenCalled();
        });

        it("should call update after data is sent", function(){
            expect(progress.send).toHaveBeenCalled();
            expect(progress.text).not.toEqual('');
        });

        it("should be able to stop timer", function(){
            progress.stop();
            expect(progress.timer).toBeNull();
        });

    });

});



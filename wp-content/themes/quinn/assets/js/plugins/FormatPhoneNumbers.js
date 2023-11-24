// Format phone numbers as they are typed
/* !(function(r) {
    r.fn.phoneFormat = function(e) {
        var t = {
                errorHtml: '<div class="error">Area code cannot start with 0 or 1.</div>',
                showErrors: !0
            },
            s = function(r) {
                var e = this.val();
                if (((filteredNum = e.replace(/[^\d]/g, "")), "0" == filteredNum.substr(0, 1) || "1" == filteredNum.substr(0, 1))) this.settings.showErrors && this.error.show(), (filteredNum = "");
                else if (this.settings.showErrors) {
                    var t = this;
                    setTimeout(function() {
                        t.error.fadeOut();
                    }, 2e3);
                }
                var s = "";
                filteredNum.length ?
                    ((s = "(" + filteredNum.substr(0, 3)),
                        filteredNum.length >= 3 && (3 != filteredNum.length || 8 != r.which) && (s += ") "),
                        (s += filteredNum.substr(3, 3)),
                        filteredNum.length >= 6 && (6 != filteredNum.length || 8 != r.which) && (s += "-"),
                        (s += filteredNum.substr(6, 4))) :
                    1 == e.length && "(" == e[0] && (s = "("),
                    this.val(s);
            };
        return this.each(function() {
            var i = r(this);
            (i.settings = r.extend({}, t, e)), i.settings.showErrors && ((i.error = r(i.settings.errorHtml)), i.error.hide(), i.after(i.error)), i.keyup(r.proxy(s, i)), i.change(r.proxy(s, i));
        });
    };
})(jQuery);
*/


// pretty-format phone inputs
jQuery(function($){
    $('[type="tel"]').mask("(999)-999-9999", {autoclear: false});
})

var FxCpcXmls = (function (FxCpcXmls, $) {
    $(function () {
        FxCpcXmls.CloneTool.init();
    });
    FxCpcXmls.CloneTool = {
        init() {
            const $postData = {action: "clone_new_xmls"};
            $('button.cpc-clone-data').on('click', function () {
                $postData['user-action'] = 'clone';
                FxCpcXmls.CloneTool.processRequest($postData);
            });
            $('button.cpc-delete-data').on('click', function () {
                $('.cpc-xml-spinner-wrap').addClass('is-submitting');
                $postData['user-action'] = 'delete';
                $('button.cpc-delete-data').hide();
                $('i.cpc-current-status').empty().prepend('<b class="dlt-message">Delete in progress...</b>');
                FxCpcXmls.CloneTool.processRequest($postData);
            });
        },
        processRequest(postData) {
            $.ajax({
                url: Fx_Chp.ajax_url, data: postData, type: "POST", dataType: 'json', success: function (response) {
                    if (response) {
                        if (response.delete) {
                            $('.cpc-xml-spinner-wrap').removeClass('is-submitting');
                            $('.cpc-delete-data').hide();
                            $('.dlt-message').empty().text(response.delete);
                            FxCpcXmls.CloneTool.reloadIframe();
                        }

                    }
                }, // error: function (xhr, ajaxOptions, thrownerror, response) {} //uncomment and log for ajax debugging
            });
        },
        reloadIframe() {
            $('#cpc-xml-hierarchy').attr('src', $('#cpc-xml-hierarchy').attr('src'));
        }
    };
    return FxCpcXmls;
})(FxCpcXmls || {}, jQuery);
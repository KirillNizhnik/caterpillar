var FxCpcHelper = (function (FxCpcHelper, $) {
    $(function () {
        FxCpcHelper.SearchTool.init();
        $('div.advanced_toggle').on("click", function () {
            $('div.advanced_toggle_content').toggle();
        });
    });
    FxCpcHelper.SearchTool = {
        $targetForm: null,
        init() {
            this.$targetForm = $('#fx_cpc_search_tool');
            $(document).on('submit', this.$targetForm, function (e) {
                e.preventDefault();
                FxCpcHelper.SearchTool.setUpPostData();
            });
        },
        setUpPostData() {
            const $postData = {action: "get_xml_for_text_match"};
            $(this.$targetForm).find('input').each(function () {
                if ($(this).attr('name') !== 'submit') {
                    if ($(this).attr('type') === 'checkbox') {
                        $postData[$(this).attr('name')] = $('#' + $(this).attr('id') + ':checked').length;
                    } else {
                        $postData[$(this).attr('name')] = $(this).val();
                    }
                }
            });
            FxCpcHelper.SearchTool.processSubmit($postData);
        },
        processSubmit(postData) {
            $.ajax({
                url: Fx_Chp.ajax_url, data: postData, type: "POST", dataType: 'json', success: function (response) {
                    if (response.standard_response) {
                        $('div.cpc_standard_response_placeholder').empty();
                        $('div.cpc_standard_response_placeholder').append(response.standard_response);
                        if (response.advanced_response) {
                            $('div.advanced_toggle_content').empty();
                            $('.cpc_advanced_response_placeholder').show();
                            $('div.advanced_toggle_content').append(response.advanced_response);
                        }
                        if (typeof response.advanced_response === 'undefined') {
                            $('.cpc_advanced_response_placeholder').hide();
                        }
                    } else {
                        $('div.cpc_standard_response_placeholder').empty();
                        $('div.cpc_standard_response_placeholder').append('<p>Something went wrong with the code for this.</p>');
                    }
                }, //error: function (xhr, ajaxOptions, thrownerror, response) {} //uncomment and log for ajax debugging
            });
        }
    };
    return FxCpcHelper;
})(FxCpcHelper || {}, jQuery);
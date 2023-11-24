var FxDsfHelper = (function (FxDsfHelper, $) {
    $(function () {
        FxDsfHelper.SearchTool.init();
        $('div.advanced_toggle').on("click", function () {
            $('div.advanced_toggle_content').toggle();
        });
    });
    FxDsfHelper.SearchTool = {
        $targetForm: null,
        init() {
            this.$targetForm = $('#fx_dsf_search_tool');
            $(document).on('submit', this.$targetForm, function (e) {
                e.preventDefault();

                FxDsfHelper.SearchTool.setUpPostData();
            });
        },
        setUpPostData() {
            const $postData = {action: "chp_search_used"};
            $(this.$targetForm).find('input').each(function () {
                if ($(this).attr('name') !== 'submit') {
                    if ($(this).attr('type') === 'checkbox') {
                        $postData[$(this).attr('name')] = $('#' + $(this).attr('id') + ':checked').length;
                    } else {
                        $postData[$(this).attr('name')] = $(this).val();
                    }
                }
            });
            FxDsfHelper.SearchTool.processSubmit($postData);
        },
        processSubmit(postData) {
            $.ajax({
                url: Fx_Chp.ajax_url, data: postData, type: "POST", dataType: 'json', success: function (response) {
                    if (response.standard_response) {
                        $('div.dsf_standard_response_placeholder').empty();
                        $('div.dsf_standard_response_placeholder').append(response.standard_response);
                        if (response.advanced_response) {
                            $('div.advanced_toggle_content').empty();
                            $('.dsf_advanced_response_placeholder').show();
                            $('div.advanced_toggle_content').append(response.advanced_response);
                        }
                        if (typeof response.advanced_response === 'undefined') {
                            $('.dsf_advanced_response_placeholder').hide();
                        }
                    } else {
                        $('div.dsf_standard_response_placeholder').empty();
                        $('div.dsf_standard_response_placeholder').append('<p>Something went wrong with the code for this.</p>');
                    }
                }, //error: function (xhr, ajaxOptions, thrownerror, response) {} //uncomment and log for ajax debugging
            });
        }
    };
    return FxDsfHelper;
})(FxDsfHelper || {}, jQuery);
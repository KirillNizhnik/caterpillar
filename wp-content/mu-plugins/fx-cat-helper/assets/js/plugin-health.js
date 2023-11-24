var FxChpHealth = (function (FxChpHealth, $) {
    $(function () {
        FxChpHealth.CloneTool.init();
        FxChpHealth.NewAuthTest.init();
    });
    FxChpHealth.CloneTool = {
        init() {
            $(document).ready(function () {
                $('#chp_plugin_health_table')
                    .DataTable();
            });
        },
    };
    FxChpHealth.NewAuthTest = {
        init() {
            $('.test-oauth-trigger').on('click', function () {
                const data = {action: "test_new_cpc_authentication"};
                $.ajax({
                    url: Fx_Chp.ajax_url, data: data, type: "POST", dataType: 'json', success: function (response) {
                        if (response) {
                            $('div.cpc_oauth_test_response').empty();
                            $('div.cpc_oauth_test_response').append('<br><b>Response: </b> ' + response);
                        } else {
                            $('div.cpc_oauth_test_response').empty();
                            $('div.cpc_oauth_test_response').append('<p>Something went wrong with the code for this.</p>');
                        }
                    }, //error: function (xhr, ajaxOptions, thrownerror, response) {} //uncomment and log for ajax debugging
                });
            });
        },
    };
    return FxChpHealth;
})(FxChpHealth || {}, jQuery);
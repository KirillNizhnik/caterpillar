jQuery(document).on('click', '#cme_activalist', function(event) { // use jQuery no conflict methods replace $ with "jQuery"

    event.preventDefault(); // stop post action

    jQuery.ajax({
        type: "POST",
        url: ajaxurl, // or '<?php echo admin_url('admin-ajax.php'); ?>'

        data: {

            action: 'wpcf7_cme_loadlistas',
            cme_idformxx: jQuery("#cme_txtcomodin").val(),
            cmeapi: jQuery("#wpcf7-campaignmonitor-api").val(),

        },


        beforeSend: function() {

            jQuery(".cme-main-fields .spinner").css('visibility', 'visible');

            // alert('before');

        },


        success: function(response) { // response //data, textStatus, jqXHR

            jQuery(".cme-main-fields .spinner").css('visibility', 'hidden');
            jQuery('#cme_panel_listamail').html(response);

            var valor = jQuery("#cme_txcomodin2").val();
            // var chm_valid ='';
            var attrclass = '';

            if (valor === '1') {

                attrclass = 'spt-response-out spt-valid';
                jQuery("#cm_apivalid .cmm").removeClass("invalid").addClass("valid");
                jQuery("#cm_apivalid .dashicons").removeClass("dashicons-no").addClass("dashicons-yes");

                jQuery(".chmp-inactive").removeClass("chmp-inactive").addClass("chmp-active");

                jQuery("#chmp-new-user").removeClass("chmp-active").addClass("chmp-inactive");



            } else {

                attrclass = 'spt-response-out';
                jQuery("#cm_apivalid .cmm").removeClass("valid").addClass("invalid");
                jQuery("#cm_apivalid .dashicons").removeClass("dashicons-yes").addClass("dashicons-no");

                jQuery(".chmp-active").removeClass("chmp-active").addClass("chmp-inactive");

                jQuery("#chmp-new-user").removeClass("chmp-inactive").addClass("chmp-active");

            }

            jQuery('#cme_panel_listamail').attr("class", attrclass);
            // jQuery('#cme_apivalid').html( chm_valid );

        },

        error: function(data, textStatus, jqXHR) {

            jQuery(".cme-custom-fields .spinner").css('visibility', 'hidden');
            alert(textStatus);

        },

    });

});



jQuery(document).on('change', '#campaignmonitor-update', function(event) { // Aug 7, 2020

    event.preventDefault(); // stop post action

    var xchk = 0;

    if (jQuery(this).prop('checked'))
        xchk = 1;

    jQuery.ajax({
        type: "POST",
        url: ajaxurl, // or '<?php echo admin_url('admin-ajax.php'); ?>'
        data: {
            action: 'wpcf7_cme_set_autoupdate',
            valcheck: xchk,
        },

        success: function(response) { // response,data, textStatus, jqXHR,

            //jQuery( '#gg-select'+ itag ).html( response );

        },
        error: function(data, textStatus, jqXHR) {

            alert(textStatus);

        },
    });
});

jQuery(document).on('click', '.cpe-trigger-log', function(event) {

    event.preventDefault(); // stop post action

    jQuery.ajax({
        type: "POST",
        url: ajaxurl, // or '<?php echo admin_url('admin-ajax.php'); ?>'

        data: {

            action: 'campaing_logload',
            cme_idformxx: jQuery("#cme_txtcomodin").attr("value"),
            cmeapi: jQuery("#wpcf7-campaignmonitor-api").attr("value"),

        },
        // error: function(e) {
        //   console.log(e);
        // },

        beforeSend: function() {

            // jQuery("#log_reset").addClass("CHIMPLogger");

        },

        success: function(response) { // response //data, textStatus, jqXHR

            jQuery('#cm_log_panel').html(response);

        },

        error: function(data, textStatus, jqXHR) {

            alert(jqXHR);

        },

    });
});

jQuery(document).on('click', '#cm_log_reset', function(event) {

    event.preventDefault(); // stop post action

    jQuery.ajax({
        type: "POST",
        url: ajaxurl, // or '<?php echo admin_url('admin-ajax.php'); ?>'

        data: {

            action: 'campaing_logreset',
            cme_idformxx: jQuery("#cme_txtcomodin").attr("value"),
            cmeapi: jQuery("#wpcf7-campaignmonitor-api").attr("value"),

        },
        // error: function(e) {
        //   console.log(e);
        // },

        beforeSend: function() {

            jQuery("#cm_log_reset").addClass("CHIMPLogger");

        },

        success: function(response) { // response //data, textStatus, jqXHR

            jQuery('#cm_log_panel').html(response);

        },

        error: function(data, textStatus, jqXHR) {

            alert(jqXHR);

        },

    });

});




jQuery(document).ready(function() {
    try {

        if (!jQuery('#wpcf7-campaignmonitor-cf-active').is(':checked'))

            jQuery('.campaignmonitor-custom-fields').hide();

        jQuery('#wpcf7-campaignmonitor-cf-active').click(function() {

            if (jQuery('.campaignmonitor-custom-fields').is(':hidden') &&
                jQuery('#wpcf7-campaignmonitor-cf-active').is(':checked')) {

                jQuery('.campaignmonitor-custom-fields').slideDown('fast');
            } else if (jQuery('.campaignmonitor-custom-fields').is(':visible') &&
                jQuery('#wpcf7-campaignmonitor-cf-active').not(':checked')) {

                jQuery('.campaignmonitor-custom-fields').slideUp('fast');
                jQuery(this).closest('form').find(".campaignmonitor-custom-fields input[type=text]").val("");

            }
        });


        jQuery(".cme-trigger").click(function() {

            jQuery(".cme-support").slideToggle("fast");

            jQuery(this).text(function(i, text) {
                return text === "Show Advanced Settings" ? "Hide Advanced Settings" : "Show Advanced Settings";
            });

            return false; //Prevent the browser jump to the link anchor
        });

        jQuery(".cpe-trigger-sys").click(function() {

            jQuery("#toggle-sys-cme").slideToggle(250);

        });

        jQuery(".cpe-trigger-log").click(function() {

            jQuery("#cm_eventlog-sys").slideToggle(250);

        });


        function cme_toggleDiv() {

            setTimeout(function() {
                jQuery(".cme-cta").slideToggle(450);
            }, 10000);

        };
        cme_toggleDiv();


    } catch (e) {

    }

});
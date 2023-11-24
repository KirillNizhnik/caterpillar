// FX Contact Form 7 Email Template
(function( $ ) {
    // Main jQuery Elements
    var $preview = $('#FX-CF7-ET-preview'),
        $CF7MailBody = $('#wpcf7-mail-body'),
        $CF7Mail2Body = $('#wpcf7-mail-2-body'),
        dataEditor = null,
        codeEditor = null,
        blockID = null;
        activeView = 'admin';

    var FX_CF7_ET = (function() {
        var initialize = function () {
            initializeEditor();
            updatePreview();
            addEvents();
        };

        var initializeEditor = function () {
            dataEditor = CodeMirror.fromTextArea(document.getElementById('FX-CF7-ET-editor-data'), {
                lineNumbers: true,
                lineWrapping: true,
                matchBrackets: true,
                foldGutter: true,
                theme: 'material-ocean',
                mode: 'application/json',
                gutters: ['CodeMirror-lint-markers', 'CodeMirror-linenumbers', 'CodeMirror-foldgutter'],
                lint: true
            });

            codeEditor = CodeMirror.fromTextArea(document.getElementById('FX-CF7-ET-editor-code'), {
                lineNumbers: true,
                lineWrapping: true,
                matchBrackets: true,
                foldGutter: true,
                gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter'],
                theme: 'material-ocean',
                mode: {
                    name: 'handlebars',
                    base: 'text/html'
                }
            });
        };

        var addEvents = function () {
            dataEditor.on('change', function () {
                updatePreview();
            });

            codeEditor.on('change', function () {
                updatePreview();
            });

            // Show admin message section / customer message section accordingly
            $('.FX-CF7-ET-admin-view-btn, .FX-CF7-ET-customer-view-btn').on('click', function (e) {
                $('.FX-CF7-ET-admin-view-btn, .FX-CF7-ET-customer-view-btn').removeClass('active');
                let $target = $(e.target);
                $target.addClass('active');
                let section_id = $target.data('section');
                $('.message_content').removeClass('active');
                $('#' + section_id).addClass('active');

                activeView = $target.data('view');

                updatePreview();
            });

            // Make sure both admin and customer views are updated before saving form to ensure global changes are applied to both
            $('[name="wpcf7-save"]').on('focus mouseover', function(e) {
                // (only applicable if mail2 active)
                if ( $('#wpcf7-mail-2-active:checked').length > 0 ) {
                    if ( activeView == 'admin')
                        updatePreview('customer');
                    else if ( activeView == 'customer')
                        updatePreview('admin');
                }
            });

        };

        var updatePreview = function (view) {
            if (typeof view == 'undefined')
                view = activeView;

            let dataStr = dataEditor.getValue();
            var templateRendered = '';
            if ( codeEditor.getValue() && dataStr ) {
                var template = Handlebars.compile( codeEditor.getValue() );

                let data = JSON.parse( dataStr );

                // if admin preview, make sure only admin_view is on
                if (view == 'admin') {
                    data.admin_view = "true";
                    data.customer_view = "";

                    // Make sure email is set to HTML
                    if ( $('#wpcf7-mail-use-html').not(':checked') ) {
                        $('#wpcf7-mail-use-html').prop('checked', true);
                    }
                } // if user preview, make sure only admin_view is on
                else if (view == 'customer') {
                    data.customer_view = "true";
                    data.admin_view = "";

                    // Make sure email is set to HTML
                    if ( $('#wpcf7-mail-2-use-html').not(':checked') ) {
                        $('#wpcf7-mail-2-use-html').prop('checked', true);
                    }
                }

                templateRendered = template( data );

                $('.FX-CF7-ET-global-btn').show();
            } else {
                $('.FX-CF7-ET-global-btn').hide();
            }

            $preview.html( templateRendered );

            if (view == 'admin') {
                $CF7MailBody.val( templateRendered );
            } else if (view == 'customer' ) {
                // auto-activate mail2 if not active
                if ( $('#wpcf7-mail-2-active').not(':checked').length > 0 ) {
                    $('#wpcf7-mail-2-active').click();
                    $('#wpcf7-mail-2-active').prop('checked', true);
                    alert('Auto-activating Mail(2) for customer. Please check the Mail(2) configuration in the Mail tab before saving.');
                }
                $CF7Mail2Body.val( templateRendered );
            }
        };

        var updateData = function (newData) {
            var dataFormatted = formatJSON(newData);
            dataEditor.setValue( dataFormatted );
        };

        var formatJSON = function (jsonData) {
            if (jsonData.length == 0) return '';
            return JSON.stringify(jsonData, null, 2);
        };

        return {
            init: initialize,
            updatePreview: updatePreview,
            updateData: updateData
        };
    })();

    FX_CF7_ET.init();

    var FX_CF7_ET_forms = (function() {
        var initialize = function () {
            addEvents();
        };

        var addEvents = function () {
            $preview.on('hover', '[data-editable]', function(event) {
                var $this = $(this);
                $this.toggleClass('data-editable');
                positionEditButton( $this.position() );
                $('#FX-CF7-ET-actions').toggle();
            });

            $('#FX-CF7-ET').on('click', '[data-editable]', function(event) {
                event.preventDefault();
                blockID = $(this).data('editable');
                editValues();
            });

            $('#FX-CF7-ET-form-content').on('submit', function(event) {
                event.preventDefault();
                formSubmission();
            });

            $('#FX-CF7-ET-form-submit').on('click', function(event) {
                event.preventDefault();
                formSubmission();
            });

            $('.FX-CF7-ET-modal-close').on('click', function(event) {
                event.preventDefault();
                toggleModal();
            });

            $('#FX-CF7-ET-form').on('click', '#FX-CF7-ET-new-field', function(event) {
                event.preventDefault();
                $('#FX-CF7-ET-form-content').append(FieldAndLabel($(this).data('blockid'), '', ''));
            });

            $('#FX-CF7-ET-form').on('click', '.FX-CF7-ET-form-remove', function(event) {
                event.preventDefault();
                if ($('#FX-CF7-ET-form .form-group').length > 1) {
                    $(this).parent('.form-group').remove();
                }
            });

            $('.FX-CF7-ET-defaults-btn').click( function() {
                dataEditor.setValue( $('#FX-CF7-ET-default-editor-data').val() );
                codeEditor.setValue( $('#FX-CF7-ET-default-editor-code').val() );
            });
        };

        var positionEditButton = function (blockPosition) {
            if (blockPosition) {
                var left = blockPosition.left || 0;
                var top = blockPosition.top || 0;
                $('#FX-CF7-ET-actions').css({ left: left - 1, top: top - 1 });
            }
        };

        var editValues = function () {
            renderForm();
            toggleModal(true);
        };

        var toggleModal = function (stayOpen) {
            if (stayOpen && !$('#FX-CF7-ET-modal').hasClass('FX-CF7-ET-modal--hidden')) return;

            $('#FX-CF7-ET-modal').toggleClass('FX-CF7-ET-modal--hidden');
        };

        var addDragEvents = function () {
            console.log('addDragEvents: ', dragula);
            dragula([document.getElementById('FX-CF7-ET-form-content')], {
                moves: function (el, container, handle) {
                    console.log('handle: ', handle);
                    return handle.classList.contains('drag-handler');
                }
            });

        }

        var renderForm = function () {
            var formData = JSON.parse( dataEditor.getValue() ),
                dataFields = formData[blockID];
            if ($.isArray(dataFields)) {
                var fields = $.map(dataFields, function(field){
                    return FieldAndLabel(blockID, field.label, field.value);
                });
                $('#FX-CF7-ET-form-header').html('Block Section: ' + blockID.toUpperCase());
                $('#FX-CF7-ET-form-content').html(fields.join(''));
                addDragEvents();
                $('#FX-CF7-ET-new-field').show();
            } else {
                var fields = $.map(dataFields, function(field, key){
                    return Field(blockID, key, field.label, field.value);
                });
                $('#FX-CF7-ET-form-header').html('Block Section: ' + blockID.toUpperCase());
                $('#FX-CF7-ET-form-content').html(fields.join(''));
                $('#FX-CF7-ET-new-field').hide();
            }
        };

        var formSubmission = function () {
            var data = JSON.parse( dataEditor.getValue() ),
                $inputs = null,
                dataBlock = data[blockID];

            if ($.isArray(dataBlock)) {
                $inputs = $('#FX-CF7-ET-form-content .form-group');
                var values = $inputs.map( function(index, element) {
                    return {
                        label: $(element).find('[name="label"]').val(),
                        value: $(element).find('[name="value"]').val(),
                    };
                }).toArray();
                data[blockID] = values;
                FX_CF7_ET.updateData(data);
            } else {
                $inputs = $('#FX-CF7-ET-form-content input, #FX-CF7-ET-form-content select, #FX-CF7-ET-form-content textarea');
                $inputs.map( function(index, element) {
                    data[blockID][$(element).attr('name')] = {
                        label: $(element).attr('placeholder'),
                        value: $(element).val()
                    };
                });
                FX_CF7_ET.updateData(data);
            }
            toggleModal();
        };

        return {
            init: initialize
        };
    })();

    FX_CF7_ET_forms.init();


    // Form Helpers
    function Field (blockID, key, label, value) {
        if (value.length > 70) {
            return '<div class="form-group"><label>' + label +'</label><textarea rows="4" class="form-control" id="' + blockID + '-' + key + '" name="' + key + '" placeholder="' + label + '">' + escapeHtml(value) + '</textarea></div>';
        }

        return '<div class="form-group"><label>' + label +'</label><input class="form-control" id="' + blockID + '-' + key + '" name="' + key + '" value="' + escapeHtml(value) + '" placeholder="' + label + '"/></div>';
    }

    function FieldAndLabel (blockID, label, value) {
        var value = value || '',
            label = label || '';
        return '<div class="form-group form-group--variant">'
                + '<span class="drag-handler" aria-hidden="true"> = </span>'
                + '<input class="form-control" id="' + blockID + '-' + label + '" name="label" value="' + escapeHtml(label) + '" placeholder="' + label + '"/>'
                + '<input class="form-control" id="' + blockID + '-' + label + '" name="value" value="' + escapeHtml(value) + '"/>'
                + '<button type="button" class="FX-CF7-ET-form-remove">&times;</button>'
                + '</div>';
    }

    function escapeHtml(text) {
      var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      };

      return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
})( jQuery );

<?php

?>
<style>
    #submit {
        display: none !important;
    }
</style>
<div class="wrap">
    <h2>Import New Categories</h2>
    <a class="button-primary" id="importLinkNew">Import New Categories</a>
</div>
<div class="result-new"></div>
<div class="dateDiv-new"></div>

<div class="wrap">
    <h2>Import Used Categories</h2>
    <a class="button-primary" id="importLinkUsed">Import Used Categories</a>
</div>
<div class="result-used"></div>
<div class="dateDiv-used"></div>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        var importLinkNew = document.getElementById('importLinkNew');
        var resultDivNew = document.querySelector('.result-new');
        var dateDivNew = document.querySelector('.dateDiv-new');

        var importLinkUsed = document.getElementById('importLinkUsed');
        var resultDivUsed = document.querySelector('.result-used');
        var dateDivUsed = document.querySelector('.dateDiv-used');

        importLinkNew.addEventListener('click', function (event) {
            event.preventDefault();
            resultDivNew.textContent = 'Start Import'
            importCategoriesWithAjax('import_new_categories_action', resultDivNew, dateDivNew,  importLinkNew);
            importLinkNew.disabled = true;
            importLinkNew.style.pointerEvents = 'none'
        });

        importLinkUsed.addEventListener('click', function (event) {
            event.preventDefault();
            resultDivUsed.textContent = 'Start Import'
            importCategoriesWithAjax('import_used_categories_action', resultDivUsed, dateDivUsed,  importLinkUsed);
            importLinkUsed.disabled = true;
            importLinkUsed.style.pointerEvents = 'none'

        });

        function importCategoriesWithAjax(action, resultDiv, dateDiv, importLink) {
            jQuery.ajax({
                type: 'post',
                url: ajaxurl,
                data: {
                    action: action,
                },
                success: function (response) {
                    if (response.success) {
                        resultDiv.textContent = 'Imported';
                    } else {
                       resultDiv.textContent = 'Not Imported'
                    }
                    setTimeout(function (){
                        resultDiv.textContent = ''
                    }, 2000)
                    importLink.disabled = false;
                    importLink.style.pointerEvents = 'auto'
                },
                error: function () {
                    alert('Failed');
                    importLink.disabled = false;
                    importLink.style.pointerEvents = 'auto'
                }
            });
        }
    });
</script>


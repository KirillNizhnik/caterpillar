<link href="https://cdn.jsdelivr.net/npm/jsoneditor@9.5.6/dist/jsoneditor.min.css" rel="stylesheet" type="text/css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    #submit {
        display: none !important;
    }

    .container {
        display: flex;
        padding: 15px;
    }

    .save, .template {
        padding: 15px;
    }

    #jsoneditor {
        padding-top: 15px;
        width: 400px;
        height: 400px;
    }

    .js-example-basic-single {
        z-index: 1000;
    }

    #save-rule {
        padding-top: 15px;
    }

    .select2-container {
        width: 100%;
    }

    .select2-selection {
        width: 100%;
    }
</style>


<div class="container">
    <div class="save">

        <label for="fam"></label><select name="fam" class="js-example-basic-single" id="fam">

        </select>

        <div id="jsoneditor"></div>
        <div id="save-rule">
            <span id="status"></span>
            <a id="save-btn" class="button-primary">Save rule</a>
        </div>
    </div>
    <div class="template">
        <span>Patterns for filling JSON</span>
        <pre id="pattern">
    {
      "sourceFamilies": ["STATIONARY GENERATOR SETS"],
      "prefix": ["3", "1"],
      "endNumber":"5",
      "end": "even", or "end":"odd",
      "range": [
          {
          "start": "10",
          "end": "19"
           },
           {
          "start": "100",
          "end": "199"
           }
      ],
      "ignore": "Bare Chassis",
      "exception": [953, 963, 973],
    }
        </pre>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsoneditor@9.5.6/dist/jsoneditor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let action = 'get_used_family_action';
    let jsonContainer = document.getElementById("jsoneditor");
    let select = document.getElementById('fam');
    let saveBtn = document.getElementById('save-btn');
    let status = document.getElementById('status');
    let editor;

    document.addEventListener('DOMContentLoaded', function () {
        init();
        saveBtn.addEventListener('click', function (){
            saveRule(editor);
        });
    });



    function init() {

        initSelect2();
        loadCategories();
        createEditor();
    }

    function initSelect2() {
        $(select).select2({
            width : '100%'
        });
        $(select).on('change', function () {
            showMetaField(editor);
        });
    }

    function loadCategories() {
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: action
            },
            success: function (response) {
                if (response.success) {
                    let categories = response.data;
                    addCategoryForDropdown(categories);
                } else {
                    console.error('Error ajax');
                }
            },
            error: function () {
                console.error('Error AJAX');
            }
        });
    }

    function addCategoryForDropdown(categories) {
        function addCategoryOptions(category, level) {
            let option = document.createElement('option');
            option.value = category.term_id;
            let hasRule = category.has_meta ? '(Has rule)' : '';
            option.text = '-'.repeat(level) + category.name + hasRule;
            select.appendChild(option);
            for (const child of category.children) {
                addCategoryOptions(child, level + 1);
            }
        }

        for (const category of categories) {
            addCategoryOptions(category, 0);
        }

    }

    function createEditor(){
        editor = new JSONEditor(jsonContainer, {
            mode:'code'
        });
        showMetaField(editor);

    }

    function showMetaField(editor) {
        let selectedCategoryId = select.value;
        action = 'load_used_equipment_meta_field';
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: action,
                category_id: selectedCategoryId
            },
            success: function (response) {
                editor.set(response.data);
            },
            error: function () {
                console.error('Error ajax');
            }
        });
    }

    function saveRule(editor) {
        let selectedCategoryId = select.value;
        let JSONData
        try{
            JSONData = editor.get();
        }catch (error){
            JSONData = [] ;
        }

        action = 'save_used_equipment_meta_field';

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: action,
                category_id: selectedCategoryId,
                JSON: JSONData
            },
            beforeSend: function () {
                saveBtn.style.pointerEvents = 'none';
                console.log('Before send');
                status.style.color = "blue";
                status.textContent = "Process...";
            },
            success: function (response) {
                console.log(response)
                if (response.success) {
                    status.style.color = "green";
                    status.textContent = "Saved";
                } else {
                    status.style.color = "red";
                    status.textContent = "Not saved, you args == null";
                }
                removeStatus();
            },
            error: function () {
                status.style.color = "red";
                status.textContent = "Not saved, no valid json";
                removeStatus();
            },
            complete: function () {
                saveBtn.style.pointerEvents = 'auto';
            }
        });
    }



    function removeStatus() {
        setTimeout(function () {
            status.style.color = "";
            status.textContent = "";
        }, 1000);
    }
</script>

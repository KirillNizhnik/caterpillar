<?php
?>

<link href="https://cdn.jsdelivr.net/npm/jsoneditor@9.5.6/dist/jsoneditor.min.css" rel="stylesheet" type="text/css">
<style>
    #submit {
        display: none !important;
    }

    .container {
        display: flex;
        width: 100%;
        padding: 15px 15px 0 15px;
    }

    .input {
        width: 500px;
        max-width: 100%;
        padding-right: 15px;
    }


    .output {
        width: 500px;
        padding-left: 15px;
    }

    .input-array {
        width: 100%;
    }

    #jsoneditor {
        padding-top: 15px;
        width: 400px;
        height: 400px;
    }

    .js-example-basic-single {
        z-index: 1000;
    }


</style>

<div>
    <div class="container">
        <label for="family-drop"></label><select id="family-drop" name="family">
            <option value="family">Family</option>
            <option value="used-family">Used Family</option>
        </select>
        <label for="group_id"></label><select id="group_id" name="group_id">
            <option value="406">406</option>
            <option value="405">405</option>
            <option value="402">402</option>
            <option value="486">486</option>
            <option value="410">410</option>
        </select>
        <label for="categories"></label><select id="categories" name="categories">
            <option value="ASPHALT DISTRIBUTORS">ASPHALT DISTRIBUTORS</option>
            <option value="BACKHOE LOADER">BACKHOE LOADER</option>
            <option value="HAY EQUIPMENT">HAY EQUIPMENT</option>
            <option value="MOBILE GENERATOR SETS">MOBILE GENERATOR SETS</option>
            <option value="MOTOR GRADERS">MOTOR GRADERS</option>
            <option value="OFF HIGHWAY TRUCKS">OFF HIGHWAY TRUCKS</option>
            <option value="SCREENS">SCREENS</option>
            <option value="SKID STEER LOADERS">SKID STEER LOADERS</option>
            <option value="SYSTEMS / COMPONENTS">SYSTEMS / COMPONENTS</option>
            <option value="TELEHANDLER">TELEHANDLER</option>
            <option value="TRACK EXCAVATORS">TRACK EXCAVATORS</option>
            <option value="TRACK LOADERS">TRACK LOADERS</option>
            <option value="TRACK TYPE TRACTORS">TRACK TYPE TRACTORS</option>
            <option value="TRACTORS">TRACTORS</option>
            <option value="TRAILERS">TRAILERS</option>
            <option value="VIBRATORY DOUBLE DRUM ASPHALT">VIBRATORY DOUBLE DRUM ASPHALT</option>
            <option value="WATER TRUCKS">WATER TRUCKS</option>
            <option value="WHEEL LOADER">WHEEL LOADER</option>
            <option value="WHEEL TRACTOR SCRAPERS">WHEEL TRACTOR SCRAPERS</option>
            <option value="PARTS">PARTS</option>
            <option value="OTHER">OTHER</option>
        </select>
    </div>
</div>
<div class="container">
    <div class="input">
        <label for="your_input1_id"></label><textarea class="input-array" placeholder="Enter model" type="text"
                                                      name="your_input_name" id="your_input1_id"></textarea>
    </div>
    <div class="result"><span class="span-result">Result:</span>
        <p id="a-result" class="a-result"></p></div>
</div>
<div class="container">
    <a id="test-btn-assign" class="button-primary">Test Assign</a>
    <span class="status-assign"></span>
</div>
<!--<div class="container">-->
<!--    <a  id="delete-posts-btn" class="button-primary"> Delete posts</a>-->
<!---->
<!--   <label for="delete-posts"></label><select id="delete-posts" name="delete-posts">-->
<!--        <option value="equipment" class="equipment">equipment</option>-->
<!--        <option value="used-equipment" class="equipment">used-equipment</option>-->
<!--    </select>-->
<!--    <span id="result-delete"></span>-->
<!--</div>-->



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsoneditor@9.5.6/dist/jsoneditor.min.js"></script>
<script>
    $(document).ready(function () {
        $("#family-drop").on("change", function () {
            const selectedFamily = $(this).val();
            if (selectedFamily === "family") {
                $("#group_id").show();
                $("#categories").hide();
            } else if (selectedFamily === "used-family") {
                $("#group_id").hide();
                $("#categories").show();
            }
        });

        $("#family-drop").trigger("change");
    });
</script>
<script>
    $(document).ready(function () {
        const btn1 = $('#test-btn-assign');
        const result1 = $('#a-result');
        const model1 = $('#your_input1_id');
        const dropdown = $('#family-drop');
        const groupIdDropDown = $('#group_id');
        const familyDropDown = $('#categories');
        btn1.on('click', function () {
            testAssign();
        });

        function testAssign() {
            const ajaxModel = model1.val();
            const taxonomy = dropdown.val();
            let additionalInfo;
            if (taxonomy === 'used-family'){
               additionalInfo = familyDropDown.val();
            }
            if(taxonomy === 'family') {
                additionalInfo = groupIdDropDown.val();
            }
            const action = 'test_assign';

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: action,
                    model: ajaxModel,
                    taxonomy: taxonomy,
                    additionalInfo: additionalInfo,
                },
                beforeSend: function () {
                    result1.text('process...');
                },
                success: function (response) {
                    result1.text(response.data);
                },
                error: function () {
                    // Handle error
                },
                complete: function () {
                    console.log('Complete');
                }
            });
        }
    });
</script>

<!--<script>-->
<!--    $(document).ready(function () {-->
<!--        const deletePostsSelect = $('#delete-posts');-->
<!--        const btnDeletePosts = $('#delete-posts-btn');-->
<!--        const resultDelete = $('#result-delete')-->
<!---->
<!--        btnDeletePosts.on('click', function () {-->
<!--            deletePosts();-->
<!--        });-->
<!---->
<!--        function deletePosts() {-->
<!---->
<!--            const postType = deletePostsSelect.val();-->
<!--            const action = 'delete-posts';-->
<!---->
<!--            $.ajax({-->
<!--                type: 'POST',-->
<!--                url: ajaxurl,-->
<!--                data: {-->
<!--                    action: action,-->
<!--                    postType: postType,-->
<!--                },-->
<!--                beforeSend: function () {-->
<!--                    resultDelete.text('process...');-->
<!--                },-->
<!--                success: function (response) {-->
<!--                    resultDelete.text('done');-->
<!--                },-->
<!--                error: function () {-->
<!--                    resultDelete.text('error')-->
<!--                },-->
<!--                complete: function () {-->
<!--                    console.log('Complete');-->
<!--                }-->
<!--            });-->
<!--        }-->
<!--    });-->
<!--</script>-->
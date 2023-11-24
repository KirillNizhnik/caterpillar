<?php
// Template for steps system with metabox styles and toggle show/hide
/*
Expects:
$steps = [
    [
        'title' => 'Title of step'
        ,'content' => 'Content of step'
    ]
];
*/

$last = count($steps)-1;
$active = empty($_GET['step']) ? 0 : ($_GET['step'] - 1);
if ($active < 0)
    $active = 0;
if ($active > $last)
    $active = $last;

?>

<div id="post-body" class="metabox-holder columns-2">
    <div id="postbox-container-2" class="postbox-container" style="width:50%">
        <div id="normal-sortables" class="meta-box-sortables ui-sortable">

        <?php foreach ($steps as $s => $step): ?>

            <?php
                $is_first = ($s == 0);
                $is_active = ($s == $active);
                $is_last  = ($s == $last);
            ?>

            <div class="postbox  hide-if-js <?php echo $is_active ? "" : "closed" ?>" style="display: block;">
                <button type="button" class="handlediv button-link" aria-expanded="true" onclick="jQuery(this).closest('.postbox').toggleClass('closed')"><span class="screen-reader-text">Toggle panel: <?php echo $step['title'] ?></span><span class="toggle-indicator" aria-hidden="true"></span></button>
                <h2 class="hndle ui-sortable-handle"><span><?php echo $step['title'] ?></span></h2>
                <div class="inside">
                    <?php echo $step['content'] ?>
                    <p>
                        <?php if (!$is_first): ?>
                            <a href="#" class='left button button-primary' onclick="jQuery(this).closest('.postbox').addClass('closed').prev('.postbox').removeClass('closed');return false;">&laquo; Previous Step</a>
                        <?php endif ?>
                        <?php if (!$is_last): ?>
                            <a href="#" class='right button button-primary' onclick="jQuery(this).closest('.postbox').addClass('closed').next('.postbox').removeClass('closed');return false;">Next Step &raquo;</a>
                        <?php else: ?>
                            <a href="options-general.php?page=cat_assistant&tab=general" class='right button button-primary'>I'm finished &raquo;</a>
                    <?php endif ?>
                        <br class='clearfix'>
                    </p>
                </div>
            </div>

        <?php endforeach ?>

        </div>
    </div>
</div>

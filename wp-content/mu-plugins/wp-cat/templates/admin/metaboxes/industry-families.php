<?php
/*
Title: Families
Context: normal
Priority: default
*/
global $post;
wp_enqueue_script( 'accordion' );

if( CAT()->usingApplications )
{
    $industries  = get_posts( array('post_type' => 'cat_industry', 'posts_per_page' => -1) );
    $application = CAT()->application($post->ID);

    $industry = $application->industry();
    $products = $application->products();
}
else
{
    $industry = CAT()->industry($post->ID);
    $products = $industry->products();
}


foreach($products as &$page) {
    if($page->type == 'page')
        unset($page->post_content);
}

$products_json =  str_replace('\'', '&rsquo;', json_encode($products));

?>
<script type="text/javascript">
    window.IndustryFamilies = JSON.parse('<?php echo $products_json; ?>');
</script>

<div class="industries-families-wrap">
    <div class="families accordion-container">
        <ul class="outer-border">
        <?php
            $classes  = CAT()->get_available_classes();
            $relation = CAT()->get_class_post_type_relation();
            $enabled  = get_option('cat_new_class_limitation');


        foreach($classes as $class_id => $name):
            if(! in_array($class_id, $enabled)) continue;

            $taxonomy = $relation[$class_id].'_family';
        ?>
            <li class="family control-section accordion-section">

                <h3 class="family__title accordion-section-title hndle">
                    <?php echo $name; ?>
                    <span class="screen-reader-text">Press return or enter to expand</span>
                </h3>

                <div class="family__terms accordion-section-content posttypediv">
                    <div class="inside">
                        <div class="tabs-panel tabs-panel-active  ">
                        <?php
                        $terms = get_terms( $taxonomy, array('orderby' => 'id') );
                        foreach($terms as $term): ?>

                            <label class="term <?php echo ($term->parent > 0) ?'has-parent' : '' ;?>" for="<?php echo $term->slug; ?>">
                                <input type="checkbox"
                                       name="<?php echo $term->slug; ?>"
                                       id="<?php echo $term->slug; ?>"
                                       value="<?php echo $term->term_id; ?>"
                                       data-name="<?php echo $term->name; ?>"
                                       data-type="term">
                                <?php echo $term->name; ?>
                            </label>

                        <?php endforeach; ?>
                        </div>
                        <p class="button-controls">
                            <span class="add-to-menu">
                                <button type="button"
                                        class="button-secondary js-submit-add-to-industry right">
                                    Add Families
                                </button>
                                <span class="spinner"></span>
                            </span>
                        </p>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>

        <li class="family control-section accordion-section">

                <h3 class="family__title accordion-section-title hndle">
                    Pages
                    <span class="screen-reader-text">Press return or enter to expand</span>
                </h3>

                <div class="family__terms accordion-section-content posttypediv">
                    <div class="inside">
                        <div class="tabs-panel tabs-panel-active  ">
                        <?php
                        $pages = get_pages();
                        foreach($pages as $page): ?>

                            <label class="term <?php echo ($page->parent > 0) ?'has-parent' : '' ;?>" for="<?php echo $page->post_name; ?>">
                                <input type="checkbox"
                                       name="<?php echo $page->post_name; ?>"
                                       id="<?php echo $page->post_name; ?>"
                                       value="<?php echo $page->ID; ?>"
                                       data-name="<?php echo $page->post_title; ?>"
                                       data-type="page">
                                <?php echo $page->post_title; ?>
                            </label>

                        <?php endforeach; ?>
                        </div>
                        <p class="button-controls">
                            <span class="add-to-menu">
                                <button type="button"
                                        class="button-secondary js-submit-add-to-industry right">
                                    Add Pages
                                </button>
                                <span class="spinner"></span>
                            </span>
                        </p>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="selected-families-wrap">
        <div class="cat-admin-bar application-form">
            <div class="application-field-wrap">

                <?php if( CAT()->usingApplications ): ?>
                <select name="industry_id">
                    <option value="">Select Industry</option>

                    <?php foreach($industries as $i): ?>
                    <option value="<?php echo $i->ID; ?>" <?php selected( $i->ID, $industry->ID); ?>>
                        <?php echo $i->post_title; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php else: ?>
                    <input type="hidden" name="industry_id" value="<?php echo $post->ID; ?>" />
                <?php endif; ?>
            </div>
        </div>
        <div class="selected-families js-selected-families" id="js-selected-families">
        </div>
    </div>
</div>

<script type="text/template" id="familyNodeTemplate">
    <div class="node">
        <h3 class="node__title"><%= name %></h3>
        <button type="button" data-id="<%= id %>" data-type="<%= type %>" class="node__remove js-remove-node">X</button>
        <input type="hidden" name="related_families[]" value="<%= object_id %>|<%= type %>" />
    </div>
</script>

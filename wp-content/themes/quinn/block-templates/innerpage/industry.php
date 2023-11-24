<?php


//acf possibilities
$link = get_sub_field('industry_link');
$name = get_sub_field('industry_name');
$learn_more = get_sub_field('learn_more_text');
$thumbnail = get_sub_field('thumbnail');
?>


    <article class="col-md-4 col-sm-6">
        <div class="image-button-box">
            <a href="<?php if($link) { echo $link; } else { the_permalink(); } ?>">
                <div class="image-button-image">
                <?php
                    $thumbnail = get_the_post_thumbnail_url(get_the_id());

                    //echo cat_sized_image( $thumbnail, array(200,200), array('itemprop' => 'image') );
                    if($thumbnail){
                        echo cat_sized_image( $thumbnail, 'full', array('itemprop' => 'image') );
                    } else {
                        echo fx_get_image_tag(7339);
                    }

                ?>
                </div>
                <div class="image-button-content">
                    <h4><?php if($name) { echo $name; } else { echo $industry->name; } ?></h4>
                    <span class="btn btn-tertiary"><?php if($learn_more) { echo $learn_more; } else { echo "Learn More"; } ?></span>
                </div>
            </a>
        </div>
    </article>

<?php

/**
 * Create an SEO-friendly image tag based on supplied arguments
 *
 * @param	mixed   $image      Image ID (integer/string) or image URL (string)
 * @param   mixed   $class      Either string or array of classes
 * @param   string  $size       Image size
 * @param   bool    $skip_lazy  Prevent WP Rocket from lazy-loading image
 * @param   array   $atts       Additional attributes to add to tag 
 *      
 * @return  string              If valid image, then <img> string; otherwise empty string
 */
function fx_get_image_tag( $image, $classes = '', string $size = 'full', bool $skip_lazy = false, array $atts = [] ): string {
    $image_id = null;

    // determine if image ID or URL
    if( is_numeric( $image ) ) {
        $image_id = absint( $image );

    // try to find ID based on URL
    } elseif( is_string( $image ) ) {
        $image_id = attachment_url_to_postid( $image );
    }

    // if still empty, check for placeholder
    if( empty( $image_id ) ) {
        $image_id = get_field( 'placeholder_img', 'option' );
	}

    // if STILL empty, return empty string
    if( empty( $image_id ) ) {
        return '';
	}

    // if classes weren't passed as string, try to form string
    if( is_array( $classes ) ) {
        $classes = implode( ' ', $classes );
	}

    // prevent lazyloading from WP Rocket?
    if( $skip_lazy && false !== strpos( $classes, 'skip-lazy' ) ) {
        $classes .= ' skip-lazy';
    }

    // combine classes with tag attributes
    $atts = array_merge( 
        [ 
            'class' => $classes 
        ], 
        $atts 
    );
    $atts = array_filter( $atts );

    // use WP's native function to generate image element
    $tag = wp_get_attachment_image( $image_id, $size, false, $atts );

    return $tag;
}


/**
 * Strip all nonalphanumeric characters from string
 *
 * @param	string	$arg    String to strip
 * @return  string          Stripped string
 */
function fx_string_strip_special( string $arg = '' ): string {
    return preg_replace( '/[^A-Za-z0-9]/', '', $arg );
}


/**
 * Pretty-print var_dump for easier readability
 *
 * @param	mixed   $var        Variable to var_dump
 * @param   bool    $esc_html   If true, will escape HTML to prevent rendering content as HTML
 * @return  void
 */
if( !function_exists( 'fx_var_dump' ) ) {
    function fx_var_dump( $var = null, bool $esc_html = false ): void {
        if( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || 'development' === wp_get_environment_type() ) {
            echo '<pre><code>'; 

            if( $esc_html && is_string( $var ) ) {
                $var = esc_html( $var );
            }

            var_dump( $var );

            echo '</code></pre>';
        }
    }
}


/**
 * Get attachment ID for client logo
 * 
 * The image for the client logo can be set in WP Admin > Theme Settings > Media Assets > Logo
 *
 * @return  int|null     Attachment ID if logo has been set in admin; otherwise, null
 */
function fx_get_client_logo_image_id() {
    $logo_id = get_field( 'logo', 'option' );

    return $logo_id ?: null;
}


/**
 * Get client telephone number
 * 
 * The phone number can be set in WP Admin > Theme Settings > Contact Info > Phone
 *
 * @param	bool    $raw    Get phone number with special characters stripped (ideal for usage with tel: protocol)
 * @return  string|null     String if phone number set in admin; otherwise, null
 */
function fx_get_client_phone_number( bool $raw = false ) {
    $phone_number = get_field( 'phone', 'option' );

    if( !empty( $phone_number ) ) {
        if( $raw ) {
            $phone_number = fx_string_strip_special( $phone_number );
        }

        return $phone_number;
    }

    return null;
}


/**
 * Get client email address
 * 
 * The email address can be set in WP Admin > Theme Settings > Contact Info > Email
 *
 * @param	bool    $antispam   Get email address with random characters converted to HTML entities to deter spambots
 * @return  string|null         String if email address is set in admin and valid; otherwise, null
 */
function fx_get_client_email( bool $antispam = false ) {
    $email_address = get_field( 'email', 'option' );

    if( !empty( $email_address ) && is_email( $email_address ) ) {
        if( $antispam ) {
            $email_address = antispambot( $email_address );
        }

        return $email_address;
    }

    return null;
}


/**
 * Get client physical address
 * 
 * The physical address can be set in WP admin > Theme Settings > Contact Info > Address
 *
 * @return  string|null     String if email address is set in admin and valid; otherwise, null
 */
function fx_get_client_address() {
    $address = get_field( 'address', 'option' );

    return $address ?: null;
}


/**
 *  Related Equipment
 */


function fx_related_equipment($atts){

    extract(
        shortcode_atts(array(
            'post_type'  => 'cat_used_machine'
        ),$atts)
    );


    $related = new WP_Query(array(
        'post_type'   => $post_type
        ,'post_status' => 'publish'
        ,'posts_per_page' => 2
    ));

    if ( $related->have_posts() ) : ?>

        <div class="related-equipment related-equipment-new">

            <h3>Related Equipment </h3>

        <?php while ( $related->have_posts() ) : $related->the_post();

            $post_id             = get_the_ID();
            $primary_image       = get_post_meta($post_id, '_thumbnail_id',true);
            $image               = wp_get_attachment_image_src( $primary_image, 'specials-image' );

            if ( has_post_thumbnail() ): $imgSrc = $image[0]; else: $imgSrc = get_template_directory_uri().'/assets/img/specials.jpg'; endif;

            $machine = CAT()->product($post_id);

            ?>

            <article class="related-equipment__item">

                <!--<img src="<?php //echo $imgSrc;?>" alt="" class="img-responsive">-->

                <div class="equipment-image"><?php echo cat_sized_image( reset($machine->images), array(220,165), array( 'class' => 'img-responsive' )  ); ?></div>

                <ul>
                    <li><b>Manufacturer</b>: <span> <?php //echo isset($machine->manufacturer) ? $machine->manufacturer : 'N/A'; ?> N/A</span></li>
                    <li><b>Model</b>: <span> <?php echo get_post_meta($post_id, 'model',true); ?> </span></li>
                    <li><b>Serial</b>: <span> <?php echo get_post_meta($post_id, 'serial_number',true); ?> </span></li>
                </ul>

                <a href="<?php the_permalink(); ?>" class="btn">More Info</a>

            </article>

        <?php endwhile; wp_reset_postdata(); ?>

        </div>

    <?php else: ?>

           <p>No Equipment Found!</p>

    <?php endif;
}

add_shortcode('related_equipment','fx_related_equipment');

function fx_find_related(){

    $product =  CAT()->product();

    $families = array();

    $taxonomy_match = $product->family->taxonomy;
    $post_match = $product->post_type;
    
    if(!empty($product->family) )
        $families[] = $product->family->slug;

    if(!empty($product->subfamily))
        $families[] = $product->subfamily->slug;

    $defaults = array(
        'post_type' => $post_match
        ,'post_status' => 'publish'
        ,'posts_per_page' => 3
		,'tax_query' =>  array(
        array(
            'taxonomy' => $taxonomy_match
            ,'field'    => 'slug'
            ,'terms'    => $families
            ,'include_children' => true
            ,'operator'         => 'IN'
        ))
    );
  
    return new WP_Query($defaults);

}

function fx_get_related_equipment() {
    $product =  CAT()->product();
    $related = fx_find_related();

    //var_dump($related->have_posts());

	?>

		<?php if ( $related->have_posts() ) : ?>
            <?php while ( $related->have_posts() ) : $related->the_post();

                $post_id             = get_the_ID();
                $primary_image       = get_post_meta($post_id, '_thumbnail_id',true);
                $image               = wp_get_attachment_image_src( $primary_image, 'specials-image' );
                if ( has_post_thumbnail() ): $imgSrc = $image[0]; else: $imgSrc = get_template_directory_uri().'/assets/img/specials.jpg'; endif;
                $machine = CAT()->product($post_id);
                $manufacturer=cat_used_manufacturer($post_id);

                ?>
                
                    <article class="product-item-block">
                    <div class="product-card-details-block">
                        <div class="product-card">
                            <div class="product-card-detail-info">
                                <a href="<?php the_permalink(); ?>" class="product-item-card">
                                    <div class="product-item-card__thumb">
                                        <?php //echo fx_get_image_tag( 417 ); 
                                        $image = reset($machine->images);
                                        if(get_field('is_image_blurry', $post_id ) == 'yes') {
                                            $image = 7339;
                                        }
                                        //var_dump($image);
                                        ?>
                                        <?php echo cat_sized_image( $image, array(436,276), array( 'class' => 'img-responsive' )  ); ?>
                                        <span class="btn btn-tertiary family-btn">View Details</span>
                                    </div>
                                    <h4 class="family-name"><?php 
                                    echo get_the_title($post_id); 
                                    //echo get_post_meta($post_id, 'model',true); 
                                    
                                    ?></h4>
                                </a>
                            </div>
                        </div>
                    </div>
                </article>

                <!--<article class="related-equipment__item">
                    <!--<img src="<?php //echo $imgSrc;?>" alt="" class="img-responsive">--><!--
                    <div class="equipment-image"><?php // echo cat_sized_image( reset($machine->images), array(220,165), array( 'class' => 'img-responsive' )  ); ?></div>
					<?php //$manufacturer=cat_used_manufacturer($post_id);?>
                    <ul>
                        <li><b>Manufacturer</b>: <span> <?php //echo $manufacturer; ?> </span></li>
                        <li><b>Model</b>: <span> <?php //echo get_post_meta($post_id, 'model',true); ?> </span></li>
                        <li><b>Serial</b>: <span> <?php //echo get_post_meta($post_id, 'serial_number',true); ?> </span></li>
                    </ul>
                    <a href="<?php //the_permalink(); ?>" class="btn">More Info</a>
                </article> -->

            <?php endwhile; wp_reset_postdata(); ?>


        <?php else: ?>

			<p> &nbsp; No Equipment Found!</p>

        <?php endif;  ?>



<?php }


function cat_used_manufacturer($postid){
	global $wpdb;

	$manufacturer_sql = "
		SELECT tr.object_id, tt.term_taxonomy_id , tt.term_id , tt.taxonomy , t.name , t.slug
		FROM $wpdb->term_taxonomy tt, $wpdb->terms t, $wpdb->term_relationships tr
		WHERE taxonomy = 'cat_used_machine_manufacturer'
		AND tt.term_id = t.term_id
		AND tt.term_taxonomy_id = tr.term_taxonomy_id
		AND tr.object_id =".$postid
	;


	$results = $wpdb->get_results( $manufacturer_sql );

	if($results):

		foreach($results as $list):
			return $list->name . ' ';
		endforeach;

	endif;


} // function end

add_action( 'wp_ajax_cat_check_url', 'cat_check_url' );
add_action( 'wp_ajax_nopriv_cat_check_url', 'cat_check_url' );
function cat_check_url(){
    $url = sanitize_text_field( $_POST['url'] );

    $parsed_url = parse_url( $url );

    $paths = explode( "/", $parsed_url['path'] );

    $response = array(
        'catalog' => '',
        'category' => ''
    );

    if( isset( $paths[1] ) && !empty( $paths[1] ) ) {
        $catalog = $paths[1];

        if( $catalog == 'used-equipment' ) $response['catalog'] = 'Used Equipment';
        elseif( $catalog == 'equipment' || $catalog == 'new-equipment' ) $response['catalog'] = 'New Equipment';

        if( isset( $paths[2] ) && !empty( $paths[2] ) && $catalog == 'used-equipment' ) {
            $category_slug = $paths[2];

            $family = 'cat_used_machine_family'; 

            $term_obj = get_term_by( 'slug', $category_slug, $family );

            if( $term_obj ) $response['category'] = $term_obj->name;
        }


        if( isset( $paths[3] ) && !empty( $paths[3] ) && $catalog == 'new-equipment' ) {
            $category_slug = $paths[3];

            $family = 'used-family';

            $term_obj = get_term_by( 'slug', $category_slug, $family );

            if( $term_obj ) $response['category'] = $term_obj->name;
        }
    }

    echo json_encode( $response );
    exit;
}
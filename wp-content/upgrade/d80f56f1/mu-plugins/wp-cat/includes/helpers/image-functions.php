<?php


function cat_sized_image_src( $image, $size='full' )
{
    if( is_numeric($image) )
    {
        $image = wp_get_attachment_image_src($image, $size);
        return $image[0];
    }
    else
    {
        $sizer = \Cat\Controllers\Image::instance();
        return $sizer->get_sized_image_url($image, $size);
    }
}


function cat_sized_image( $image, $size='full', $attrs=array() )
{
    $defaults = array(
        'alt' => ''
    );

    $atts = '';
    $attrs = array_merge($defaults, $attrs);

    if( ! is_string($image) )
    {
        if( isset($image->title)
            && ! empty($attrs['alt'])
            && ! empty($image->title)
        ){
            $attrs['alt'] = $image->title;
        }

        if(isset($image->type) && $image->type == 'wp_attachment')
            $image = $image->ID;
        elseif ( !isset($image->src) or $image->src == 'http://s7d2.scene7.com/is/image/Caterpillar/no-image-available' )
        {
            if (is_file(get_stylesheet_directory().'/images/coming-soon.jpg'))
                $image = get_stylesheet_directory_uri().'/images/coming-soon.jpg';
            else
                $image = 'https://quinncompany.com/wp-content/uploads/2021/09/default.jpg';
        }
        else
            $image = $image->src;
    }

    if( ! empty($attrs) )
    {
        foreach($attrs as $key => $value) {
            $atts .= ' '.$key.'="'.$value.'"';
        }
    }

    return '<img src="'.cat_sized_image_src($image, $size).'"'.$atts.' />';
}




/**
 * Return formated string for sizing images
 * @param  [type] $size [description]
 * @return [type]       [description]
 */
function cat_size_string($size)
{
    global $_wp_additional_image_sizes;
    $sizer = '';

    if($size == 'full'){
        return '';
    } else {

        if(is_string($size)){
            if( isset( $_wp_additional_image_sizes )
                AND isset( $_wp_additional_image_sizes[ $size ] )
            ){
               $size = array(
                    $_wp_additional_image_sizes[ $size ]['width']
                    ,$_wp_additional_image_sizes[ $size ]['height']
                );
            }
        }

        if(is_array($size))
        {
            $sizer .= '?';
            $width = false;

            if(intval($size[0]) != 9999){
                $sizer .= 'wid='.$size[0];
                $width=true;
            }
            if(intval($size[1]) != 9999){
                $sizer .= ($width) ? '&' : '';
                $sizer .='hei='.$size[1];
            }

            $sizer .='&op_sharpen=1&qlt=100';
        }
    }

    return $sizer;
}


function youtube_video_thumbnail($video)
{
    if( is_object($video) ) {
        $video = $video->src;
    }

    $split = explode('?v=', $video);
    $id = explode('&', $split[1]);
    $id = $id[0];

    if(count($split) < 2) {
        $segment = count(explode('/', $video)) - 1;
        $id = explode('/', $video);
        $id = $id[$segment];
    }

    return '<img src="https://img.youtube.com/vi/'.$id.'/default.jpg" alt="" />';
}

function youtube_video_src($video)
{
    
    if( is_object($video) ) {
        $video = $video->src;
    }
    if (strpos($video,'youtube') !== false) {
        return $video;
    }

    $split = explode('?v=', $video);
    $id = explode('&', $split[1]);
    $id = $id[0];

    if(count($split) < 2) {
        $segment = count(explode('/', $video)) - 1;
        $id = explode('/', $video);
        $id = $id[$segment];
    }

    return 'https://youtube.com/embed/' . $id;
}


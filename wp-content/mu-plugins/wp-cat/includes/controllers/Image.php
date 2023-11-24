<?php
namespace Cat\Controllers;

class Image
{
    protected static $instance;
    protected $upload_dir;
    protected $cache_life;

    protected function __construct()
    {
        // setup our paths
        $upload_dir = wp_upload_dir();
        $this->upload_dir = array(
             'dir' => $upload_dir['basedir'] . '/equipment'
            ,'url' => $upload_dir['baseurl'] . '/equipment'
        );

        if( ! file_exists($this->upload_dir['dir']) ){
            mkdir($this->upload_dir['dir']);
        }

        $this->cache_life = apply_filters('wp_cat_used_image_cache_time', 2592000 ); // defaults to 30 days

        // make sure we have the file functions available
        include_once ABSPATH.'wp-admin/includes/file.php';
    }

    /**
     * Singleton method
     * @return class instance of the class
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }


    /**
     * Returns the closest matched sized image url
     * @param  string       $filename  file to match (typically a url)
     * @param  string|array $size      the size of the image to look for
     * @return string                  url to the image
     */
    public function get_sized_image_url($image, $size='full', $crop=false)
    {

        $base_name = str_replace('http://s7d2.scene7.com/is/image/Caterpillar/', '', $image);

        $sizes     = $this->sizes();
        if( $size === 'full' )
            return $image;

        if( is_string($size) && ! isset($sizes[$size]) )
            return $image;

        if( is_string($size) ){
            $crop = $sizes[$size]['crop'];
            $size = array( $sizes[$size]['width'], $sizes[$size]['height']);
        }
        $sized_name = $this->file_sized_name($base_name, $size[0], $size[1]);

        // if we have the image and its not passed cache storage date
        // we can return the url without creating it again
        if( ! $this->should_create_image($sized_name) )
            return $this->upload_dir['url'].'/'.$sized_name;

        $tmp_file    = download_url( $image ); // downloads full image to temp dir

        $sized_image = $this->size_image($tmp_file, $sized_name, $size[0], $size[1], $crop);


        // remove temp image
        unlink($tmp_file);

        if( false !== $sized_image ){

            return $this->upload_dir['url'].'/'.$sized_name;
        }

        return $image;
    }


    /**
     * save a sized image
     * @param  [type]  $image  [description]
     * @param  [type]  $name   [description]
     * @param  integer $width  [description]
     * @param  integer $height [description]
     * @param  integer $crop   [description]
     * @return [type]          [description]
     */
    public function size_image($image, $name, $width=0, $height=0, $crop=false)
    {
        // Return an implementation that extends WP_Image_Editor
        $image = wp_get_image_editor( $image );

        if ( is_wp_error( $image ) )
            return false;

        $current_size = $image->get_size();

        if( $width && $height) {
            // if($width > $current_size['width'] OR $height > $current_size['height'] )
            //     return false;

            $image->resize( $width, $height, $crop );
        }

        // set quality to 100 so we aren't
        // recompressing cdn images
        $image->set_quality( 100 );

        if( $image->save( $this->upload_dir['dir'].'/'.$name ) ) {
            return $this->upload_dir['dir'].'/'.$name;
        }

        return false;
    }

    /**
     * Check to see if an image exists or needs refreshed
     * @param  [string] $image_name  name of the file excluding path
     * @return [bool]   true if the file should create sizes
     */
    private function should_create_image($image_name)
    {
        $file = $this->upload_dir['dir'].'/'.$image_name;

        if(! file_exists($file) ) {
            return true;
        }

        $filemtime = @filemtime($file);

        if (!$filemtime or (time() - $filemtime >= $this->cache_life))
           return true;

       return false;
    }


    /**
     * return an array of all the current images sizes
     * @return [array] current registed image sizes
     */
    private function sizes()
    {
        global $_wp_additional_image_sizes;
        $sizes = array();

        foreach( get_intermediate_image_sizes() as $s )
        {
            $sizes[ $s ] = array();

            if( in_array( $s, array( 'thumbnail', 'medium', 'large' ) ) )
            {
                $sizes[ $s ]['width']  = get_option( $s . '_size_w' );
                $sizes[ $s ]['height'] = get_option( $s . '_size_h' );
                $sizes[ $s ]['crop']   = ($s === 'thumbnail') ? 1 : 0;
            }
            else
            {
                if( isset( $_wp_additional_image_sizes )
                    AND isset( $_wp_additional_image_sizes[ $s ] )
                ){
                    $sizes[ $s ] = array(
                        'width'   => $_wp_additional_image_sizes[ $s ]['width']
                        ,'height' => $_wp_additional_image_sizes[ $s ]['height']
                        ,'crop'   => $_wp_additional_image_sizes[ $s ]['crop']
                    );
                }
            }
        }

        return $sizes;
    }

    /**
     * generate the name of an image with size attached
     * @param  [string] $name   name of the file
     * @param  [int]    $width  width size
     * @param  [int]    $height height size
     * @return [string]         the filename in format name-widthxheight.ext
     */
    private function file_sized_name($name, $width, $height)
    {
        $info = pathinfo($name);
        if( empty($info['extension']) )
            $info['extension'] = 'jpg';

        return sanitize_title($info['filename']).'-'.$width.'x'.$height.'.'.$info['extension'];
    }
}
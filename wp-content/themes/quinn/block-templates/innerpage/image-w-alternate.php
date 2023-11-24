<style>
    .image-button-eng-es + .image-button {
        padding-top: 0;
    }
</style>

<section class="image-button image-button-eng-es" style="padding:76px 0 0 !important;">
    <div class="container">
        <div class="image-button-header">
             <?php 
             $img = get_field( 'english_image' );

             $request = explode('/', $_SERVER['REDIRECT_SCRIPT_URL']);
             //if( '50.205.49.193' === $_SERVER['REMOTE_ADDR'] ) {
             //       var_dump($request);
            //}
             if($request[1] == 'es'){
                 $img = get_field( 'spanish_image' );
             }
             $img_markup =  str_replace('width="600"', 'width="900"', fx_get_image_tag( $img, 'aligncenter size-full'));
             echo $img_markup;
             ?>
        </div>
    </div>
</section>


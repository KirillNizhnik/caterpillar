<?php
    $zipcode  = ( isset($_POST['zipcode']) ) ? intval($_POST['zipcode']) : '';
    $distance = ( isset($_POST['distance']) ) ? intval($_POST['distance']) : '';
?>
<div class="widget-even widget wpcm_search_by_zip"
    ><h3><?php echo $title; ?></h3>
        <form class="find-location clearfix js-widget-search-by-zip form-box home-cta-location-search" action="<?php echo get_permalink( 529 );?>" method="POST">
        <input class="find-rep__field js-sbz-zipcode" name="zipcode" type="text" value="Enter Zip Code" onFocus="if (this.value == 'Enter Zip Code') {this.value = '';}"
            onBlur="if (this.value == '') {this.value = 'Enter Zip Code';}">
        <input name="action" type="hidden" value="rep_search">
        <button class="btn btn-secondary " type="submit">Go</button>
    </form>
</div>



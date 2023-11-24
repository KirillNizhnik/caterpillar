<?php
    $zipcode  = ( isset($_POST['zipcode']) ) ? intval($_POST['zipcode']) : '';
    $distance = ( isset($_POST['distance']) ) ? intval($_POST['distance']) : '';
?>
<div class="widget-even widget wpcm_search_by_zip"
    ><h3><?php echo $title; ?></h3>
        <form class="find-rep__form clearfix js-widget-search-by-zip form-box" action="<?php echo get_permalink( 24 );?>" method="POST">
        <input class="find-rep__field js-sbz-zipcode" name="zipcode" type="text" value="Enter Zip Code" onFocus="if (this.value == 'Enter Zip Code') {this.value = '';}"
            onBlur="if (this.value == '') {this.value = 'Enter Zip Code';}">
        <input name="action" type="hidden" value="rep_search">
        <button class="button button--secondary button--small push--top" type="submit">Go</button>
    </form>
</div>



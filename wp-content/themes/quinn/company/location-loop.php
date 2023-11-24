<article class="card card--location" itemscope itemtype="http://schema.org/LocalBusiness">
    <div class="card__primary-info">
        <h4 class="card__title" itemprop="name"><span class="icon-map-pin-alt"></span> <?php echo $location->title; ?></h4>
        <address>
            <?php echo $location->address; ?>
        </address>
    </div>
    <div class="card__secondary-info">
        <div class="row">
            <div class="col-sm-6">
                <?php echo $location->phone; ?> 
              </div>
              <div class="col-sm-6 col-md-5 col-md-offset-1">
                    <?php echo $location->hours; ?>
                    <a class="button button--primary button--block" target="_blank" href="<?php echo $location->directions; ?>">Get Directions</a>
              </div>
          </div>
    </div>
    <meta itemprop="branchof" content="Quinn Company">
    <meta itemprop="url" content="<?php echo $location->url; ?>">
</article>
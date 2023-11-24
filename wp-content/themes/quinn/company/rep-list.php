<?php
$user_zip = '';

if( ! empty($zip_searched) && $zip_searched != 'Enter Your Zip Code') {
    $user_zip = $zip_searched;
}
?>
<script type="text/javascript" src='https://quinncompany.com/wp-content/themes/quinn/assets/js/plugins/slick.js' id='fx_slick' defer></script>
<div id="wpcm_rep_search_by_zip">
		<div class="col-xxs-12">
          <h3>Connect With A Quinn Sales Representative</h3>

          <form id="rep-search" class="rep-search" method="POST">
            <label for="zipcode">Enter Your Zip Code </label>
            <input name="zipcode" class="zipcode" id="zipcode" type="text" value="<?php echo $user_zip; ?>" />
            <button type="submit" name="submit" class="rep-submit" id="submit" class="button button--primary"/>Go</button>
          </form>
		</div>
</div>

<section class="representatives-list rep-result clearfix col-xxs-12" id="representatives-list">

<?php
    if ( ! empty($representatives) && !empty($user_zip)):  ?>
	 <div class="row border-bottom soft-bottom push-bottom">
    <?php foreach ($representatives as $industry => $reps): ?>
    <div class="col-xxs-12 col-sm-3">



        <?php foreach($reps as $rep): ?>

            <article class="rep card card--product">
                <div class="card__primary-info">

                    <div class="specs specs--list specs--rep clearfix">

                    <?php if( ! empty($rep->photo) ): ?>
                        <div class="rep-photo"><img src="<?php echo $rep->photo; ?>" class="img-responsive" alt=""/></div>
                    <?php endif; ?>

					<h4 class="rep__name card__title"><?php echo $rep->name; ?></h4>

                   <?php if( $rep->title ): ?>
                        <div><?php echo $rep->title; ?></div>
                    <?php endif; ?>

                    <?php if( $rep->phone ): ?>
                        <div class="rep-info"><b>Phone: </b><a href="<?php echo $rep->phone; ?>"><?php echo $rep->phone; ?></a></div>
                    <?php endif; ?>

                    <?php if( $rep->email ): ?>
                        <div class="rep-info"><a class="mail-to" target="_blank" href="mailto:<?php echo $rep->email; ?>">Email Me</a></div>
                    <?php endif; ?>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
		</div>
		<?php endforeach; ?>
	</div>



    <?php else: ?>
        <?php if( !empty($user_zip) ): ?>
            <p>No reps were found in your zip code. Please contact us directly for inquiries.</p>
        <?php else: ?>
            <p>Enter your zip code above to find your closest representative.</p>
        <?php endif; ?>

    <?php endif; ?>
<?php //die();?>
</section>
<?php //var_dump($representatives); ?>
<script type="text/template" id="repTemplate">
	 <div class="col-xxs-12 col-sm-3 soft-bottom push-bottom">
		<article class="rep card card--product">
			<div class="card__primary-info">
				<div class="specs specs--list specs--rep clearfix">
				<% if ( photo ) { %>
					<div class="rep-photo"><img src="<%= photo %>" class="img-responsive" alt=""/></div>
				<% } %>
					<h4 class="rep__name card__title"><%= name %></h4>
					<div>
						<% if ( title ) { %><%= title %> <% } %>
					</div>
					<div class="rep-info"><b>Phone: </b><a href="<%= phone_link %>"> <%= phone  %></a>
					</div>
					<div class="rep-info"><a class="mail-to" target="_blank" href="mailto:<%= email %>">Email Me</a></div>
				</div>
			</div>
		</article>
    </div>
</script>
<script type="text/template" >
	 // Get the input field
    var input = document.getElementById("zipcode");
    // Execute a function when the user releases a key on the keyboard
    input.addEventListener("keyup", function(event) {
      // Number 13 is the "Enter" key on the keyboard
      if (event.keyCode === 13) {
        // Cancel the default action, if needed
        //event.preventDefault();
        // Trigger the button element with a click
        document.getElementById("submit").click();
      }
    });
</script>

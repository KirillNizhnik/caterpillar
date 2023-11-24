<?php
/*  Copyright 2010-2022 Renzo Johnson (email: renzo.johnson at gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

add_action( 'wp_ajax_campaing_logreset',  'campaing_logreset' );
add_action( 'wp_ajax_no_priv_campaing_logreset',  'campaing_logreset' );

add_action( 'wp_ajax_campaing_logload',  'campaing_logload' );
add_action( 'wp_ajax_no_priv_campaing_logload',  'campaing_logload' );

add_action( 'wp_ajax_wpcf7_cme_set_autoupdate',  'wpcf7_cme_set_autoupdate' );
add_action( 'wp_ajax_no_wpcf7_cme_set_autoupdate',  'wpcf7_cme_set_autoupdate' );


function wpcf7_cme_set_autoupdate() {
	global $wpdb;

	$valuecheck = isset( $_POST['valcheck'] ) ? $_POST['valcheck'] : 0 ;

	if ( get_option( 'campaignmonitor-update' ) !== false ) {
        update_option( 'campaignmonitor-update', $valuecheck );

    } else {
      $deprecated = null;
      $autoload = 'no';
      add_option( 'campaignmonitor-update', $valuecheck, $deprecated, $autoload );
    }

	wp_die();
}

function cme_html_log_view (){
?>
		<div id="sys-dev">

			<div id="cm_eventlog-sys" class="highlight" style="margin-top: 1em; margin-bottom: 1em; display: none;">
				<h3>Log Viewer</h3><input id="cm_log_reset" type="button" value="Log Reset" class="button button-primary" style="width:15%;">

			<pre><code id="cm_log_panel" ><?php cme_get_log_array ()  ?></code></pre>

	 </div>

</div>
<?php
}

function cme_get_log_array () {
    $default = array() ;
    $log = get_option ('cme_db_issues_log', $default  ) ;

    $cme_log = '' ;

    foreach ( $log as $item ) {

      $cme_log .= "\n" . 'Date : ' . $item['datetxt'];
      $cme_log .= "\n" . '==== Start Log ====' . "\n";
      $cme_log .= $item ['content'] .  "\n";
      $cme_log .= print_r ( $item ['object'],true );
      $cme_log .= "\n" . '==== End Log ====' . "\n" ;

    }

    echo $cme_log;

}


function campaing_logreset () {

    global $wpdb;

    $cme_db_logdb = new cme_db_log( 'cme_db_issues', 1,'api' );
    $res = $cme_db_logdb->cme_log_delete_db() ;

    $cme_log = 'Your Log is clean now!';
    $cme_log .= cme_get_log_array () ;

    echo $cme_log;

    wp_die();

}

function campaing_logload () {

    global $wpdb;

    cme_get_log_array () ;

    wp_die();

}



function wpcf7_cm_add_campaignmonitor($args) {
	$cf7_cm_defaults = array();
	$cme_txcomodin = $args->id();
	$cf7_cm = get_option( 'cf7_cm_'.$args->id(), $cf7_cm_defaults );

	$host = esc_url_raw( $_SERVER['HTTP_HOST'] );
	$url = $_SERVER['REQUEST_URI'];
	$urlactual = $url;

	//Incluir validacion del API

  $cme_txcomodin = $args->id() ;
  $listatags = wpcf7_cme_form_tags();
  // probando si cambio

  if ( ( ! isset( $cf7_cm['listatags'] ) ) or is_null( $cf7_cm['listatags'] ) ) {
      unset( $cf7_cm['listatags'] );
      $cf7_cm = $cf7_cm + array( 'listatags' => $listatags ) ;
      update_option( 'cf7_cm_'.$args->id(), $cf7_cm );
  }

  $logfileEnabled = ( isset( $cf7_cm['logfileEnabled'] ) ) ? $cf7_cm['logfileEnabled']  : 0 ;

  $cmeapi = ( isset( $cf7_cm['api'] )   ) ? $cf7_cm['api'] : null ;

	$apivalid = ( isset( $cf7_cm['api-validation'] )   ) ? $cf7_cm['api-validation'] : null ;

	$listdata = ( isset( $cf7_cm['lisdata'] )   ) ? $cf7_cm['lisdata'] : null ;


	$cm_valid = '<span class="cmm valid"><span class="dashicons dashicons-yes"></span>API Key</span>';
  	$cm_invalid = '<span class="cmm invalid"><span class="dashicons dashicons-no"></span>API Key</span>';


  include SPARTAN_CME_PLUGIN_DIR . '/lib/view.php';

}


function resetlogfile_cm() {
	if ( isset( $_REQUEST['cme_reset_log'] ) ) {

		$cme_debug_logger = new cme_Debug_Logger();

		$cme_debug_logger->reset_cme_log_file( 'log.txt' );
		$cme_debug_logger->reset_cme_log_file( 'log-cron-job.txt' );
		echo '<div id="message" class="updated notice notice-success is-dismissible"><p>Debug log files have been reset!</p></div>';
	}
}

function wpcf7_cm_save_campaignmonitor($args) {

	if(!empty($_POST)){


		$default = array () ;
		$cf7_cm = get_option ( 'cf7_cm_'.$args->id(), $default  ) ;

		$apivalid = ( isset( $cf7_cm['api-validation'] ) ) ? $cf7_cm['api-validation'] : 0   ;
		$listdata = ( isset( $cf7_cm['lisdata'] ) ) ? $cf7_cm['lisdata'] : 0   ;

		$globalarray = $_POST['wpcf7-campaignmonitor'] ;

		if ( !isset( $_POST['wpcf7-campaignmonitor']['api-validation'] ) )
				$globalarray += array ('api-validation' => $apivalid  ) ;

		if ( !isset( $_POST['wpcf7-campaignmonitor']['lisdata'] )  ) {
				$globalarray += array ('lisdata' => $listdata  ) ;
		}

		update_option( 'cf7_cm_'.$args->id(), $globalarray );
	}
}
add_action( 'wpcf7_after_save', 'wpcf7_cm_save_campaignmonitor' );



function show_cm_metabox ( $panels ) {

	$new_page = array(
		'cme-Extension' => array(
			'title' => __( 'Cmpgn Monitor Lite', 'contact-form-7' ),
			'callback' => 'wpcf7_cm_add_campaignmonitor'
		)
	);

	$panels = array_merge($panels, $new_page);

	return $panels;

}
add_filter( 'wpcf7_editor_panels', 'show_cm_metabox' );



function spartan_cme_author_wpcf7( $cme_supps, $class, $content, $args  ) {

	$cf7_cm_defaults = array();
	$cf7_cm = get_option( 'cf7_cm_'.$args->id(), $cf7_cm_defaults );

  $cfsupp = ( isset( $cf7_cm['cf-supp'] ) ) ? $cf7_cm['cf-supp'] : 0;


  if ( 1 == $cfsupp ) {

	 	$cme_supps .= cme_referer();
	 	$cme_supps .= cme_author();

	 } else {
	 	$cme_supps .= cme_referer();
	 	$cme_supps .= CME_AUTH_COMM;
	 }

	return $cme_supps;

}
add_filter('wpcf7_form_response_output', 'spartan_cme_author_wpcf7', 40, 4);



function wpcf7_cm_subscribe($obj) {

	//var_dump ('Paso 0');

	$cf7_cm = get_option( 'cf7_cm_'.$obj->id() );
	$idform = 'cf7_cm_'.$obj->id() ;

	$submission = WPCF7_Submission::get_instance();

  	$logfileEnabled = isset($cf7_cm['logfileEnabled']) && !is_null($cf7_cm['logfileEnabled']) ? $cf7_cm['logfileEnabled'] : false;

	//var_dump ('Paso 1');

	  if( $cf7_cm ) {
		$subscribe = false; //++

		$regex = '/\[\s*([a-zA-Z_][0-9a-zA-Z:._-]*)\s*\]/';
		$callback = array( &$obj, 'cf7_cm_callback' );

		$email = cf7_cm_tag_replace( $regex, $cf7_cm['email'], $submission->get_posted_data() );
		$name = cf7_cm_tag_replace( $regex, $cf7_cm['name'], $submission->get_posted_data() );

		$lists = cf7_cm_tag_replace( $regex, $cf7_cm['list'], $submission->get_posted_data() );
		$listarr = explode(',',$lists);


		if( isset($cf7_cm['accept']) && strlen($cf7_cm['accept']) != 0 ) {

			$accept = cf7_cm_tag_replace( $regex, '['. $cf7_cm['accept'] . ']', $submission->get_posted_data() );

			if(strlen($accept) > 0)
					$subscribe = true;
			

		} else {

			$subscribe = true;

		}

		//var_dump ('$subscribe 1') ;
		//var_dump ($subscribe);

		$CustomFields[] ="";

		for($i=1;$i<=20;$i++) {

			if( isset($cf7_cm['CustomKey'.$i]) && isset($cf7_cm['CustomValue'.$i]) && strlen(trim($cf7_cm['CustomValue'.$i])) != 0 ) {

				$CustomFields[] = array('Key'=>trim($cf7_cm['CustomKey'.$i]), 'Value'=>cf7_cm_tag_replace( $regex, trim($cf7_cm['CustomValue'.$i]), $submission->get_posted_data() ) );

			}

		}

		if( isset($cf7_cm['resubscribeoption']) && strlen($cf7_cm['resubscribeoption']) != 0 ){

			$ResubscribeOption = true;

		} else {

			$ResubscribeOption = false;
		}

		//var_dump ('Paso 2');
		//var_dump ('$subscribe 2') ;
		//var_dump ($subscribe);

		if($subscribe && $email != $cf7_cm['email']){

			try {


					$apiKey = $cf7_cm['api'];
					$listId = trim($listarr[0]) ;

				 	$subscriber = array(
						'EmailAddress' => $email,
						'Name' => $name,
						'CustomFields' => $CustomFields,
						'Resubscribe' => $ResubscribeOption,
						'RestartSubscriptionBasedAutoresponders' => true,
						'ConsentToTrack' => 'Yes' );

					$url = sprintf('https://api.createsend.com/api/v3.2/subscribers/%s.json', $listId);

					$vc_headers = array(
        										'headers' => array(
            													'Authorization' => 'Basic ' . base64_encode($apiKey . ':x')
        																			),
        										'body' => wp_json_encode($subscriber)
    												 );

					$resultsend = wp_remote_post($url, $vc_headers );
					//var_dump ('Paso 3');

					$resultfinal = $resultsend;

					$cme_db_log = new cme_db_log( 'cme_db_issues',  $logfileEnabled,'api',$idform );
					$cme_db_log->cme_log_insert_db(1, 'Subscribe Response: ' , $resultfinal  );

			} catch ( Exception $e ) {

		 		$cme_db_log = new cme_db_log( 'cme_db_issues' , $logfileEnabled,'api',$idform );
				$cme_db_log->cme_log_insert_db(4, 'Contact Form 7 response: Try Catch  ' . $e->getMessage()  , $e  );

      }

		}

	}


}
add_action( 'wpcf7_before_send_mail', 'wpcf7_cm_subscribe' );



function cf7_cm_tag_replace( $pattern, $subject, $posted_data, $html = false ) {
	if( preg_match($pattern,$subject,$matches) > 0)
	{

		if ( isset( $posted_data[$matches[1]] ) ) {
			$submitted = $posted_data[$matches[1]];

			if ( is_array( $submitted ) )
				$replaced = join( ', ', $submitted );
			else
				$replaced = $submitted;

			if ( $html ) {
				$replaced = strip_tags( $replaced );
				$replaced = wptexturize( $replaced );
			}

			$replaced = apply_filters( 'wpcf7_mail_tag_replaced', $replaced, $submitted );

			return stripslashes( $replaced );
		}

		if ( $special = apply_filters( 'wpcf7_special_mail_tags', '', $matches[1] ) )
			return $special;

		return $matches[0];
	}
	return $subject;
}



function cme_ext_author_form_class_attr( $class ) {

  $class .= ' cmonitor-ext-' . SPARTAN_CME_VERSION;
  return $class;

}
add_filter( 'wpcf7_form_class_attr', 'cme_ext_author_form_class_attr' );


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

add_action( 'wp_ajax_wpcf7_cme_loadlistas',  'wpcf7_cme_loadlistas' );
add_action( 'wp_ajax_no_priv_wpcf7_cme_loadlistas',  'wpcf7_cme_loadlistas' );



function wpcf7_cme_loadlistas() {
	global $wpdb;

	$cf7_cm_defaults = array();
	$cme_idformxx = 'cf7_cm_'. wp_unslash( $_POST['cme_idformxx'] );
	$cmeapi = isset( $_POST['cmeapi'] ) ? $_POST['cmeapi'] : 0 ;

	$cf7_cm = get_option( $cme_idformxx, $cf7_cm_defaults );

	$tmppost = $cf7_cm ;

	$logfileEnabled = isset( $cf7_cm['logfileEnabled'] ) ? $cf7_cm['logfileEnabled'] : 0  ;
	$logfileEnabled = ( is_null( $logfileEnabled ) ) ? false : $logfileEnabled;

  unset( $tmppost['api'],$tmppost['api-validation'],$tmppost['lisdata'] );


	$apivalid = 0;
	$apivalid = isset( $tmppost['api-validation'] ) ? $tmppost['api-validation'] : 0  ;


	$tmp = wpcf7_cme_listasasociadas( $cmeapi,$logfileEnabled,$cme_idformxx,$apivalid );



	$listdata = $tmp['lisdata'];
	$tmppost = $tmppost + $tmp ;

  	$listatags = $cf7_cm['listatags'] ;

  	$tmppost = $tmppost + array( 'api' => $cmeapi,'api-validation'=> $apivalid  );

	update_option( $cme_idformxx,$tmppost );

	$cf7_cm = get_option( $cme_idformxx, $cf7_cm_defaults );
  	cme_html_panel_listmail( $apivalid,$listdata,$cf7_cm );

	wp_die();
}



function cme_html_panel_listmail( $apivalid, $listdata, $cf7_cm ) {

  $vlist = ( isset( $cf7_cm['list'] )   ) ? $cf7_cm['list'] : ' ' ;
  $i = 0 ;

  $count = 0 ;
  $count = !is_null ( $listdata ) && is_array( $listdata ) ? count ( $listdata['lists'] ) : 0 ;


  ?>
    <small><input type="hidden" id="cme_txcomodin2" name="wpcf7-campaignmonitor[cme_txtcomodin2]" value="<?php echo( isset( $apivalid ) ) ? esc_textarea( $apivalid ) : ''; ?>" /></small>
  <?php

    if ( isset( $apivalid ) && '1' == $apivalid ) {
    ?>
      <label for="wpcf7-campaignmonitor-list"><?php echo esc_html( __( 'These are all your ' . $count .' campaing monitor lists: '  , 'wpcf7' ) ); ?></label><br />
      <select id="wpcf7-campaignmonitor-list" name="wpcf7-campaignmonitor[list]">
      <?php
      foreach ( $listdata['lists'] as $list ) {
        $i = $i + 1 ;

        ?>
        <option value="<?php echo $list['ListID'] ?>"
          <?php if ( $vlist == $list['ListID'] ) { echo 'selected="selected"'; } ?>>
          <?php echo $i .' - '.  $list['Name'].' - Unique id: '.$list['ListID'].'' ?></option>
        <?php
      }
      ?>
      </select>
     <?php
  }
}



function cme_html_selected_tag ($nomfield,$listatags,$cf7_cm,$filtro) {

if ( $nomfield != 'email' )  {
    $r = array_filter( $listatags, function( $e ) use ($filtro) {
          return $e['basetype'] == $filtro or $e['basetype'] == 'textarea'  ;
        });
} else {
  $r = array_filter( $listatags, function( $e ) use ($filtro) {
          return $e['basetype'] == $filtro ;
        });
}

$listatags =   $r ;


  $ggCustomValue = ( isset( $cf7_cm[$nomfield] ) ) ? $cf7_cm[$nomfield] : ' ' ;


  $ggCustomValue = ( ( $nomfield =='email' && $ggCustomValue == ' ' )  ? '[your-email]':$ggCustomValue   );

     ?>
      <select class="chm-select" id="wpcf7-campaignmonitor-<?php echo $nomfield; ?>"
                name="wpcf7-campaignmonitor[<?php echo $nomfield; ?>]" style="width:95%">
                <?php if ( $nomfield != 'email'  ) { ?>
                    <option value=" "
                    <?php  if ( $ggCustomValue == ' ' ) { echo 'selected="selected"'; } ?>>
                    <?php echo (($nomfield=='email') ? 'Required by MailChimp': 'Choose.. ') ?></option>
         <?php
                   }
            foreach ( $listatags as $listdos ) {
              $vfield = '['. trim( $listdos['name'] ) . ']' ;
              if ( 'opt-in' != trim( $listdos['name'] )  && '' != trim( $listdos['name'] ) ) {
              ?>
                <option value="<?php echo $vfield ?>" <?php if (  trim( $ggCustomValue ) == $vfield ) { echo 'selected="selected"'; } ?>>
                  <?php echo '['.$listdos['name'].']' ?> <span class="cme-type"><?php echo ' - type :'.$listdos['basetype'] ; ?></span>
               </option>
                <?php
              }
           }
		    ?>
         </select>
        <?php
}



function wpcf7_cme_getcodclient( $apikey, $logfileEnabled, $idform = '',$apivalid ) {
	try {


 	  $cme_db_log = new cme_db_log('cme_db_issues', $logfileEnabled,'api',$idform );

		$url = 'https://api.createsend.com/api/v3.2/clients.json';

		$vc_headers = array(
        										'headers' => array(
            													'Authorization' => 'Basic ' . base64_encode($apikey . ':x')
        																			)
    												 );

		$resultsend = wp_remote_get($url, $vc_headers );
	  $resultbody = wp_remote_retrieve_body( $resultsend );

		$resp = json_decode( $resultbody, True );

		if (!(is_array( $resp )) or is_null( $resp ) ){
			return -1;
		}

		if (isset ( $resp[0]['ClientID'] ))
			return $resp[0]['ClientID'] ;
		else
			return -1;

		if ( is_wp_error ( $resultsend ) ) {
			$cme_db_log->cme_log_insert_db(4, 'Cod Client - Response:'  , 'No Cod Cli : ' . $resp  ) ;
			return -1 ;
		}

	} catch (Exception $e) {


			$cme_db_log = new cme_db_log( 'cme_db_issues', $logfileEnabled,'api',$idform );

			$cme_db_log->cme_log_insert_db(4, 'Cod Client - Response:', 'No Cod Cli : ' . $e->getMessage()  , $e  ) ;
			return -1;

	}


}



function wpcf7_cme_listasasociadas( $apikey, $logfileEnabled, $idform = '',&$apivalid ) {
	try {

		$cod_cli = wpcf7_cme_getcodclient ( $apikey, $logfileEnabled, $idform = '',$apivalid ) ;

		$cme_db_log = new cme_db_log('cme_db_issues', $logfileEnabled,'api',$idform );

			$api   = $apikey;
			$dc    = explode("-",$api);

		  $url = "https://api.createsend.com/api/v3.2/clients/$cod_cli/lists.json";

			$vc_headers = array(
															'headers' => array(
																				'Authorization' => 'Basic ' . base64_encode($api . ':x')
																								)
															 );

			$resultsend = wp_remote_get($url, $vc_headers );

			if ( is_wp_error ( $resultsend ) ) {

					$list_data 	= array(
					'id'  => 0,
					'name' => 'sin lista',
					) ;

					$tmp = array( 'lisdata' => array('lists' => $list_data ));

					$cme_db_log->cme_log_insert_db(4, 'List ID - Response :' , $resultsend  ) ;

					return $tmp;
			}
			$resultbody = wp_remote_retrieve_body( $resultsend );
			$resp = json_decode( $resultbody, True );


			$tmp = array( 'lisdata' => array('lists' => $resp ) );

			$apivalid = 0;
			if ( isset( $resp['Code'] ) )
				if ( $resp['Code'] === 200  ) {
					$apivalid = 1;
				}
				else {
					$apivalid = 0;
				}
			else
				$apivalid = 1;


			$cme_db_log->cme_log_insert_db(1, 'List ID - Response:' , $resp  ) ;

			return $tmp;

	} catch (Exception $e) {
		$list_data 	= array(
		    'id'  => 0,
				'name' => 'sin lista',
		);
		$tmp = array( 'lisdata' => array('lists' => $list_data ));


		$cme_db_log = new cme_db_log( 'cme_db_issues', $logfileEnabled,'api',$idform );

		$cme_db_log->cme_log_insert_db(1, 'List ID - Result: error Try Catch ' . $e->getMessage()  , $e  ) ;
		return $tmp;

	}
}



function cme_new_usr() {
  $new_user ='';
  $new_user .='<h2>';
  $new_user .='<a href="https://chimpmatic.com/how-to-find-your-mailchimp-api-key'.  vc_utm(). 'NewUserMC-api" class="helping-field" target="_blank" title="get help with MailChimp API Key:"> Learn how find your Mailchimp API Key following 5 easy steps.</a>';
  $new_user .='</h2>';

  echo $new_user;

}



function wpcf7_cme_form_tags() {
	$manager = WPCF7_FormTagsManager::get_instance();
	$form_tags = $manager->get_scanned_tags();
	return $form_tags;
}

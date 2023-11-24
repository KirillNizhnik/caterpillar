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

global $wpdb;

function vc_cme_utm() {

  global $wpdb;

  $utms  = '?utm_source=CmpgnMonitor';
  $utms .= '&utm_campaign=w' . get_bloginfo( 'version' ) .'c' . WPCF7_VERSION . ( defined( 'WPLANG' ) && WPLANG ? WPLANG : 'en_US' ) . '';
  $utms .= '&utm_medium=cme-' . SPARTAN_CME_VERSION . '';
  $utms .= '&utm_term=F' . ini_get( 'allow_url_fopen' ) . 'C' . ( function_exists( 'curl_init' ) ? '1' : '0' ) . 'P' . PHP_VERSION . 'S' . $wpdb->db_version() . '';
  // $utms .= '&utm_content=';

  return $utms;

}



function cme_html_selected_tag_optin ($listatags,$cf7_cm) {

  $filtro = 'checkbox';
  /*echo ('<pre>') ;
    var_dump ( $listatags ) ;
  echo ('</pre>');*/

  $r = array_filter( $listatags, function( $e ) use ($filtro) {
          return ( $e['basetype'] == $filtro or $e['basetype'] == 'acceptance' )  ;
        });

  $listatags = $r ;
  $accept = ( isset( $cf7_cm[ 'accept' ] )   ) ? $cf7_cm[ 'accept' ] : ' ' ;

  ?>

    <select class="cme-select" id="wpcf7-campaignmonitor-accept"
      name="wpcf7-campaignmonitor[accept]" style="width:35%" >
        <span> Required Acceptance Field   <a href="<?php echo CME_URL ?>/campaignmonitor-opt-in-checkbox" class="helping-field" target="_blank" title="get help with Required Acceptance Field - Opt-in"> Help <span class="red-icon dashicons dashicons-sos"></span>
        <option value=" "
            <?php if ( $accept == ' ' ) { echo 'selected="selected"'; } ?>>
            <?php echo 'Choose.. ' ?></option>
       <?php
          foreach ( $listatags as $listdos ) {
            if ( 'opt-in' != trim( $listdos['name'] )  && '' != trim( $listdos['name'] ) ) {
            ?>
              <option value="<?php echo $listdos['name'] ?>"
                <?php if ( $accept == $listdos['name'] ) { echo 'selected="selected"'; } ?>>
                <?php echo '&#91;'.$listdos['name'].'&#93; ' ?> <span class="awb-type"><?php echo ' - type :'.$listdos['basetype'] ; ?></span> </option>
              <?php
            }
         }
      ?>

    </select>

<?php
}



function cme_panel_gen ($apivalid,$listdata,$cf7_cm,$listatags,$cme_txcomodin) {
  ?>
   <div class="mystery">

   <input type="hidden" id="cme_txtcomodin" name="wpcf7-campaignmonitor[cme_txtcomodin]" value="<?php echo( isset( $cme_txcomodin ) ) ? esc_textarea( $cme_txcomodin ) : ''; ?>" style="width:0%;" />


<table class="form-table">
  <tbody>
    <tr>
      <td>
        <span id="cme_panel_listamail" >
          <?php cme_html_panel_listmail( $apivalid, $listdata, $cf7_cm); // Get listas ?>
        </span>
        <small class="description">Hit the Connect button to load your lists <a href="<?php echo CME_URL . vc_cme_utm() ?>" class="helping-field" target="_blank" title="get help with Campaignmonitor List ID:"> Learn More</a></small>
      </td>

      <td></td>

    </tr>
  </tbody>
</table>


<table class="form-table">
  <tbody>
    <tr>
      <td><label for="wpcf7-campaignmonitor-email"><?php echo esc_html( __( 'Subscriber Email: [EMAIL] ', 'wpcf7' ) ); ?> <span class="cme-required" > Required</span></label><br />
         <?php cme_html_selected_tag ('email',$listatags,$cf7_cm,'email') ;  ?>
        <small class="description">MUST be an [email-tag] <a href="<?php echo CME_URL . vc_cme_utm() ?>" class="helping-field" target="_blank" title="get help with Subscriber Email:"> Learn More</a></small></td>

      <td><label for="wpcf7-campaignmonitor-name"><?php echo esc_html( __( 'Subscriber Name: [NAME] ', 'wpcf7' ) ); ?></label><br />
         <?php cme_html_selected_tag ('name',$listatags,$cf7_cm,'text') ; ?>
        <small class="description"> This may be sent as Name <a href="<?php echo CME_URL . vc_cme_utm() ?>" class="helping-field" target="_blank" title="get help with Subscriber name:"> Learn More</a></small></td>
    </tr>
  </tbody>
</table>

  </div>

<?php
}


?>
<div class="cme-main-fields">

  <table class="form-table">
    <tbody>
      <tr>
        <td>

          <div id="cm_apivalid" >
              <h2>Cmpgn Monitor <span class="cm-lite">Lite</span> <?php echo isset( $apivalid ) && '1' == $apivalid ? $cm_valid : $cm_invalid ; ?> <span class="mc-code  mc-dbbl">CME<?php echo SPARTAN_CME_VERSION . '.CF7' . WPCF7_VERSION . '.WP' . get_bloginfo( 'version' ) . '.P' . PHP_VERSION . '.SQ' . $wpdb->db_version() ?></span></h2>
          </div>

          <p class="mail-field">
            <label for="wpcf7-campaignmonitor-api"><?php echo esc_html( __( 'Client API Key:', 'wpcf7' ) ); ?> </label><br />
            <input type="text" id="wpcf7-campaignmonitor-api" name="wpcf7-campaignmonitor[api]" class="wide" size="70" placeholder="Enter Your API Here!" data-new="EGQ+7KmIL73tUa/i1mdboBUDuNKQJUm1cFEIxY5OZ5oAZ8VBXUvkomqVjeJ5yuk3usxZy2L57MueOQx+BJhny3g/fHV9a7/dELnRgIyYdRdz70AC5NK9RkE5+SIQX8a9CD+0JllKnwuW3s8rrL2flg==" data-old="d58e18da56063030d8eb9acb6068442e409ad89170b10232" value="<?php echo (isset($cf7_cm['api']) ) ? esc_attr( $cf7_cm['api'] ) : ''; ?>" />
                <span><input id="cme_activalist" type="button" value="Connect and Fetch Your Mailing Lists" class="button button-primary" style="width:35%;" /><span class="spinner"></span></span>
            <small class="description">512a2673a8fc4e588499e82e2d43680d100a824e8ba55394 <-- A number like this <a href="<?php echo CME_URL . vc_cme_utm() ?>" class="helping-field" target="_blank" title="get help with MailChimp API Key:"> Get more help <span class="red-icon dashicons dashicons-admin-links"></span></a></small>
          </p>
        </td>
      </tr>
    </tbody>
  </table>


  <div id="cme_panel_ajagen" class="<?php echo ( ( $apivalid == 1  ) ? 'chmp-active' : 'chmp-inactive' ) ;  ?>">
        <?php  cme_panel_gen ($apivalid,$listdata,$cf7_cm,$listatags,$cme_txcomodin) ;    ?>
  </div>




  <div class="cme-container cme-support" style="display:none">

      <table class="form-table campaignmonitor-custom-fields">

        <tbody>
          <tr>
            <td  colspan="2"><p>In the following fields, you can use these mail-tags: <?php echo cme_mail_tags(); ?></p></td>
          </tr>

          <tr></tr>
          <tr></tr>

          <?php for($i=1;$i<=13;$i++){ ?>
          <tr>
            <td><label for="wpcf7-campaignmonitor-CustomValue<?php echo $i; ?>"><?php echo esc_html( __( 'Contact form mail-tag '.$i.':', 'wpcf7' ) ); ?></label><br />
              <input type="text" id="wpcf7-campaignmonitor-CustomValue<?php echo $i; ?>" name="wpcf7-campaignmonitor[CustomValue<?php echo $i; ?>]" class="wide" placeholder="[your-mail-tag]" value="<?php echo (isset( $cf7_cm['CustomValue'.$i]) ) ?  esc_attr( $cf7_cm['CustomValue'.$i]) : '' ;  ?>"  />

            </td>
            <td><label for="wpcf7-campaignmonitor-CustomKey<?php echo $i; ?>"><?php echo esc_html( __( 'Campaignmonitor Custom Field Name '.$i.':', 'wpcf7' ) ); ?></label><br />
              <input type="text" id="wpcf7-campaignmonitor-CustomKey<?php echo $i; ?>" name="wpcf7-campaignmonitor[CustomKey<?php echo $i; ?>]" class="wide" placeholder="example-field" value="<?php echo (isset( $cf7_cm['CustomKey'.$i]) ) ?  esc_attr( $cf7_cm['CustomKey'.$i]) : '' ;  ?>"  />
            </td>
          </tr>
          <?php } ?>

        </tbody>
      </table>

        <?php include SPARTAN_CME_PLUGIN_DIR . '/lib/xin.php'; ?>

  </div>



   <div class="">
  <p class="p-author"><a type="button" aria-expanded="false" class="cme-trigger a-support ">Show Advanced Settings</a> &nbsp; <a class="cpe-trigger-sys a-support ">Get System Information</a> &nbsp; <a class="cpe-trigger-log a-support ">View Debug Logger</a></p>
  </div>


  <?php include SPARTAN_CME_PLUGIN_DIR . '/lib/system.php'; ?>
  <?php  echo cme_html_log_view() ; ?>


    <div class="dev-cta cme-cta welcome-panel" style="display: none;">

    <div class="welcome-panel-content">
    <p>Hello. My name is Renzo, I <span alt="f487" class="dashicons dashicons-heart red-icon"> </span> WordPress and I develop this FREE plugin to help users like you. I drink copious amounts of coffee to keep me running longer <span alt="f487" class="dashicons dashicons-smiley red-icon"> </span>. If you've found this plugin useful, please consider making a donation.</p>
    <p>Would you like to <a class="button-primary" href="<?php echo CME_DON ?>" target="_blank">buy me a coffee?</a></p>

    </div>

    </div>



</div>


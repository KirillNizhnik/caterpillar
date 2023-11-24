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



function cme_author() {

	$author_pre = 'Contact form 7 Campaign Monitor extension by ';
	$author_name = 'Renzo Johnson';
	$author_url = '//renzojohnson.com';
	$author_title = 'Renzo Johnson - Web Developer - Campaign Monitor - contact form 7';

	$cme_author = '<p style="display: none !important">';
  $cme_author .= $author_pre;
  $cme_author .= '<a href="'.$author_url.'" ';
  $cme_author .= 'title="'.$author_title.'" ';
  $cme_author .= 'target="_blank">';
  $cme_author .= ''.$author_title.'';
  $cme_author .= '</a>';
  $cme_author .= '</p>'. "\n";

  return $cme_author;
}



function cme_referer() {


  if(isset($_SERVER['HTTP_REFERER'])) {

    $cme_referer_url = esc_url( $_SERVER['HTTP_REFERER'] );

  } else {

    $cme_referer_url = esc_url( 'Direct Visit' );

  }

	$cme_referer = '<p style="display: none !important"><span class="wpcf7-form-control-wrap referer-page">';
  $cme_referer .= '<input type="text" name="referer-page" ';
  $cme_referer .= 'value="'.$cme_referer_url.'" ';
  $cme_referer .= 'data-value="'.$cme_referer_url.'" ';
  $cme_referer .= 'size="40" class="wpcf7-form-control wpcf7-text referer-page" aria-invalid="false">';
  $cme_referer .= '</span></p>'. "\n";

  return $cme_referer;
}



function cme_getRefererPage( $form_tag ) {

  if ( $form_tag['name'] == 'referer-page' ) {

    $form_tag['values'][] = esc_url( $_SERVER['HTTP_REFERER'] );

  }

  return $form_tag;

}

if ( !is_admin() ) {
        add_filter( 'wpcf7_form_tag', 'cme_getRefererPage' );
}



function cme_init_constants(){

  define( 'CME_URL', '//renzojohnson.com/contributions/contact-form-7-campaign-monitor-extension' );
  define( 'CME_AUTH', '//renzojohnson.com' );
  define( 'CME_AUTH_COMM', '<!-- campaignmonitor extension by Renzo Johnson -->' );
  define( 'CME_NAME', 'Campaign Monitor Extension' );
  define( 'CME_SETT', admin_url( 'admin.php?page=wpcf7&post='.cm_get_latest_item().'&active-tab=4' ) );
  define( 'CME_DON', 'https://www.paypal.me/renzojohnson' );

}
add_action( 'init', 'cme_init_constants' );



function cm_get_latest_item(){
    $args = array(
            'post_type'         => 'wpcf7_contact_form',
            'posts_per_page'    => -1,
            'fields'            => 'ids',
        );
    // Get Highest Value from CF7Forms
    $form = max(get_posts($args));
    $out = '';
    if (!empty($form)) {
        $out .= $form;
    }
    return $out;
}



function wpcf7_form_cme_tags() {

  $manager = class_exists('WPCF7_FormTagsManager') ? WPCF7_FormTagsManager::get_instance() : WPCF7_ShortcodeManager::get_instance(); // ff cf7 4.6 and earlier
  $form_tags = $manager->get_scanned_tags();
  return $form_tags;
}


function cme_mail_tags() {

  $listatags = wpcf7_form_cme_tags();
  $tag_submit = array_pop($listatags);
  $tagInfo = '';

    foreach($listatags as $tag){

      $tagInfo .= '<span class="mailtag code used">[' . $tag['name'].']</span>';

    }

  return $tagInfo;

}


if (!function_exists('chimpmatic_tags')) {
  function chimpmatic_tags( $output, $name, $html ) {

    if ( '_domain' == $name ) {
      $output = chimpmatic_domain();
    }

    if ( '_formID' == $name ) {
      $output = chimpmatic_form_id();
    }


    return $output;

  }
}
add_filter( 'wpcf7_special_mail_tags', 'chimpmatic_tags', 10, 3 );


if (!function_exists('chimpmatic_add_form_tag_posts')) {
  function chimpmatic_add_form_tag_posts() {

    wpcf7_add_form_tag('_domain', 'chimpmatic_domain');
    wpcf7_add_form_tag('_formID', 'chimpmatic_form_id');

  }
}
add_action('wpcf7_init', 'chimpmatic_add_form_tag_posts', 11);


if (!function_exists('chimpmatic_domain')) {
  function chimpmatic_domain() {

    $strToLower       = strtolower(trim( get_home_url() ));
    $httpPregReplace  = preg_replace('/^http:\/\//i', '', $strToLower);
    $httpsPregReplace = preg_replace('/^https:\/\//i', '', $httpPregReplace);
    $wwwPregReplace   = preg_replace('/^www\./i', '', $httpsPregReplace);
    $explodeToArray   = explode('/', $wwwPregReplace);
    $finalDomainName  = trim($explodeToArray[0]);

    return $finalDomainName;

  }
}


if (!function_exists('chimpmatic_form_id')) {
  function chimpmatic_form_id() {

    $wpcf7 = WPCF7_ContactForm::get_current();
    $res = $wpcf7->id();

    return $res;

  }
}
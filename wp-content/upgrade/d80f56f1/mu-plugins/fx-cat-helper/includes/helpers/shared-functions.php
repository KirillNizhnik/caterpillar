<?php
/*
* Helper file for shared functions and functionality.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Simple CPC check to make sure the necessary components are present to run.
 *
 * @return void
 */
function fx_cpc_dependency_checker() {
	if ( fx_cat_helper_tools()->is_cli() ) {
		if ( ! get_option( 'cat_new_sales_channel_code' ) ) {
			WP_CLI::error( 'It appears the sales channel code is missing for this cat installment. This command cannot run without it. Please be sure that it is properly set in the plugin.' );

		} elseif ( ! get_option( 'cat_new_secret_api_code' ) ) {
			WP_CLI::error( 'It appears the secret api code is missing for this cat installment in the plugin settings. This command cannot run without it. Please be sure that it is properly set in the plugin.' );

		} elseif ( ! ( get_option( 'cat_new_class_limitation' ) ) ) {
			WP_CLI::error( 'No valid classes have been selected. This command cannot run without them. Go set some in the plugin settings.' );
		}
	}
}

/**
 * Simple DSF check to make sure the necessary components are present to run.
 *
 * @return void
 */
function fx_dsf_dependency_checker() {
	if ( fx_cat_helper_tools()->is_cli() ) {
		if ( ! get_option( 'cat_used_feed_url' ) ) {
			WP_CLI::error( 'The used feed url/DSF-Data URL is not set. Please set it in the wp settings or specify the feed url using the customurl flag.' );
		}
	}
}

/**
 * Get auth token for API
 *
 * @return string $token Bearer token for API auth
 */
function fx_fetch_cpc_token() {
	$token        = '';
	$response_raw = wp_remote_request(
		'https://fedlogin.cat.com/as/token.oauth2',
		[
			'method'  => 'POST',
			'body'    => [
				'grant_type' => 'client_credentials',
				'scope'      => 'read:all',
			],
			'headers' => [ 'Authorization' => 'Basic ' . base64_encode( CAT()->fetch_sales_channel_code_user() . ':' . CAT()->fetch_api_auth_secret_key() ) ],
		]
	);
	$response     = json_decode( $response_raw['body'], false );
	if ( isset( $response->access_token ) ) {
		$token = $response->access_token;
	} else {
		echo wp_json_encode( $response ); //To view error in the devtools network tab
		return new WP_Error( $response->error_description, 1 ); //To view in the error logs if the error happens when running the cron
	}
	return $token;
}

/**
 * Returns the XML tree from a url
 *
 * @param  [string] $url the url to get the xml from
 *
 * @return array|string|WP_Error xml tree from the url
 */
function fx_get_cpc_xml( $url ) {
	$xml = false;
	try {
		//The auth is only used when calling this function which is only used in the new feeds.
		$token    = fx_fetch_cpc_token();
		$response = wp_remote_request(
			$url,
			[
				'method'  => 'GET',
				'headers' => [ 'Authorization' => 'Bearer ' . $token ],
			]
		);
		$raw      = wp_remote_retrieve_body( $response );
		$raw      = str_ireplace( 'http://s7d2.scene7.com', 'https://s7d2.scene7.com', $raw );
		$xml      = $raw;
	} catch ( Exception $e ) {
		return new WP_Error( 'XML Failed', __( $e ) );
	}
	if ( ! $xml ) {
		return new WP_Error( 'XML Failed', __( 'Unable to load XML file - there may be a syntax error' ) );
	}
	return $xml;
}

/**
 * Basic meta query for post id based on cpc id
 *
 * @param $product_id
 *
 * @return mixed
 */
function fx_fetch_post_by_equip_id( $product_id ) {
	global $wpdb;
	if ( fx_cat_helper_tools()->is_cli() ) {
		WP_CLI::log( 'Querying the database ...' );
	}
	$meta_query = $wpdb->get_results( 'SELECT * FROM `' . $wpdb->postmeta . "` WHERE meta_key='equipment_id' AND meta_value='" . $product_id . "'" );
	if ( isset( $meta_query[0]->post_id ) ) {
		return $meta_query[0]->post_id;
	}
	return $meta_query;
}

/**
 * Return response of cpc xml based on text match for easy iteration.
 *
 * @param $text
 * @param $class_id
 *
 * @return array|string|string[]|void|WP_Error
 */
function get_cpc_class_xml_for_text_match( $text, $class_id = '' ) {
	if ( ! empty( $class_id ) ) {
		$url      = fx_cat_helper_tools()->cpc_xml_base . fx_cat_helper_tools()->dealer_code . '/' . $class_id . 'tree_' . fx_cat_helper_tools()->lang_code . '.xml';
		$response = fx_get_cpc_xml( $url );
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		if ( str_contains( $response, $text ) ) {
			if ( fx_cat_helper_tools()->is_cli() ) {
				WP_CLI::log( WP_CLI::colorize( '%GPossible product identified at: ' . $url . '%n' ) );
			}
			return $response;
		} else {
			if ( fx_cat_helper_tools()->is_cli() ) {
				WP_CLI::log( 'No text match detected for cpc id of: ' . $text . ' in class: ' . $class_id . ' - exiting.' );
			}
			return '';
		}
	} else {
		if ( fx_cat_helper_tools()->is_cli() ) {
			WP_CLI::log( 'Searching for cpc id in classes available.' );
		}
		$product_found = false;
		foreach ( fx_cat_helper_tools()->accessible_class_ids as $class_id ) {
			$url      = fx_cat_helper_tools()->cpc_xml_base . fx_cat_helper_tools()->dealer_code . '/' . $class_id . 'tree_' . fx_cat_helper_tools()->lang_code . '.xml';
			$response = fx_get_cpc_xml( $url );
			if ( is_wp_error( $response ) ) {
				return $response;
			}
			if ( strpos( (string) $response, (string) $text ) !== false ) {
				if ( fx_cat_helper_tools()->is_cli() ) {
					WP_CLI::log( WP_CLI::colorize( '%GPossible product identified at: ' . $url . '%n' ) );
				}
				$product_found = true;
				break;
			} else {
				if ( fx_cat_helper_tools()->is_cli() ) {
					WP_CLI::log( 'No text match detected for this class of id ' . $class_id . '... proceeding to next class.' );
				}
			}
		}
		if ( $product_found === true ) {
			return $response;
		} else {
			return '';
		}
	}
}

/**
 * Favorite goodbye message
 *
 * @return void
 */
function cat_cli_goodbye_msg() {
	if ( fx_cat_helper_tools()->is_cli() ) {
		WP_CLI::log( WP_CLI::colorize( '%BEnjoy and spread positivity - bye!%n' ) );
	}
}

<?php


/**
 * These functions are intended to be used solely within the FX Block Asset Manager plugin. 
 * 
 * If the function is intended to be used in other plugins or in the theme, please add the function to 
 * public-functions.php.
 */



/**
 * Convert argument to array with no falsey values
 *
 * @param	mixed   $arg        Argument of any type
 * @param   string  $separator  Separator for passing to explode()   
 * @return  array
 */
function fx_bam_ensure_clean_array( $arg, string $separator = ' ' ): array {
	if( is_string( $arg ) ) {
		$arg = explode( $separator, $arg );
	} elseif( !is_array( $arg ) ) {
		$arg = (array)$arg;
	}

	// cleanup array
	$arg = array_values( $arg );
	$arg = array_filter( $arg ); // remove null values
	$arg = array_map( 'trim', $arg );
	$arg = array_unique( $arg );

	return $arg;
}


/**
 * Determine if site is in debug/development mode
 * 
 * @internal 		This is a one-to-one copy of fx_debug_mode_enabled function in theme
 *
 * @param   bool    $include_dev_mode   Include check for development mode
 * @return  bool                        True, if in debug mode
 */
function fx_bam_debug_mode_enabled( bool $include_dev_mode = true ): bool {

    // check for constants set in wp-config
    if( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        return true;
    }

    // conditionally check if site is in development mode
    if( $include_dev_mode && 'development' === wp_get_environment_type() ) {
        return true;
    }

    return false;
}


/**
 * Get file url based on file path
 * 
 *
 * @param	string	$url    File URL
 * @return  mixed           String, if valid file; otherwise, false
 */
function fx_bam_get_file_src_from_path( string $path ) {
	$path = fx_bam_normalize_path( $path );
	$abspath = fx_bam_normalize_path( ABSPATH, true );

	if( is_file( $path ) ) {
		if( false !== strpos( $path, $abspath ) ) {
			return str_replace( 
				$abspath, 
				trailingslashit( site_url() ),
				$path
			);

		// sites in version control may use "shared" paths
		} elseif( false !== strpos( $path, '/shared/' ) ) {
			$wp_plugin_dir = fx_bam_normalize_path( WP_PLUGIN_DIR, true );

			if( false !== strpos( $path, $wp_plugin_dir ) ) {
				$path = str_replace( $wp_plugin_dir, '', $path );
				return plugins_url( $path );
			}
		}
	}

	return false;
}


/**
 * Get non-symlinked, normalized file path
 * 
 * @since 	0.2.8
 *
 * @param	string	$file_path 				Raw file path
 * @param 	bool 	$add_trailing_slash 	If true, add trailing slash
 * @return 	string 							Normalized file path
 */
function fx_bam_normalize_path( string $path, bool $add_trailing_slash = false ): string {
	$path = realpath( $path );
	$path = wp_normalize_path( $path );

	if( $add_trailing_slash ) {
		$path = trailingslashit( $path );
	}

	return $path;
}


/**
 * Check if current request is a REST request
 * 
 * @internal  		Will be obsolete if wp_is_rest_request is accepted (https://core.trac.wordpress.org/ticket/42061)
 * 
 * @since 	0.2.6
 *
 * @return 	bool 	True, if REST request
 */
function fx_bam_is_rest_request() {
	return ( defined( 'REST_REQUEST' ) && REST_REQUEST );
}


/**
 * Check if current request is an AJAX request
 * 
 * @since 	0.2.6
 *
 * @param 	bool 	$check_json 	Conditionally check if request is a JSON request
 * @return 	bool 					True, if AJAX/JSON request
 */
function fx_bam_is_ajax_json_request( $check_json = true ) {
	$is_ajax = wp_doing_ajax();

	if( !$is_ajax && $check_json ) {
		$is_ajax = wp_is_json_request();
	}

	return $is_ajax;
}


/**
 * Show warning about deprecated function
 * 
 * @since 	0.2.6
 *
 * @param	string	$function 		Function name
 * @param 	string 	$version 		Plugin version when function was deprecated
 * @param 	string 	$replacement 	Replacement function
 *
 * @return	void
 */
function fx_bam_handle_deprecated_function( $function, $version, $replacement = '' ): void {
	if( fx_bam_is_rest_request() || fx_bam_is_ajax_json_request() ) {
		do_action( 'deprecated_function_run', $function, $replacement, $version );

		$warning = sprintf( 'The function: "%s" has been deprecated since version %s.', $function, $version );
		$warning .= ( $replacement ) ? sprintf( ' Please use the function: "%s" instead.', $replacement ) : '';
		error_log( $warning );

	} else {
		_deprecated_function( $function, $version, $replacement );
	}
}


/**
 * Show warning about deprecated argument
 * 
 * @since 	0.2.6
 *
 * @param	string	$function	Function name
 * @param 	string 	$argument 	Argument name
 * @param 	string 	$version	Plugin version when argument was deprecated
 * @param 	string 	$message 	Details about argument change
 *
 * @return	void
 */
function fx_bam_handle_deprecated_argument( $function, $argument, $version, $message = '' ): void {
	if( fx_bam_is_rest_request() || fx_bam_is_ajax_json_request() ) {
		do_action( 'deprecated_argument_run', $function, $message, $version );

		$warning = sprintf( 
			'The argument: "%s" for the function: "%s" has been deprecated since version %s.',
			$argument,
			$function,
			$version
		);
		$warning .= ( $message ) ? sprintf( ' %s', $message ) : '';
		error_log( $warning );

	} else {
		_deprecated_argument( $function, $version, $message );
	}
}


/**
 * Show warning about deprecated hook
 *
 * @since 	0.2.6
 *
 * @param	string	$hook 			Hook name
 * @param 	string 	$version 		Plugin version when hook was deprecated
 * @param 	string 	$replacement 	Replacement function
 * @param 	string 	$message 		Additional details regarding change
 *
 * @return	void
  */
function fx_bam_handle_deprecated_hook( $hook, $version, $replacement = '', $message = '' ): void {
	if( fx_bam_is_rest_request() || fx_bam_is_ajax_json_request() ) {
		do_action( 'deprecated_hook_run', $hook, $replacement, $version, $message );

		$warning = sprintf( 'The hook: "%s" has been deprecated since version %s.', $hook, $version );
		$warning .= ( $replacement ) ? sprintf( ' Please use the hook: "%s" instead.', $replacement ) : '';
		$warning .= ( $message ) ? sprintf( ' %s', $message ) : '';
		error_log( $warning );		

	} else {
		_deprecated_hook( $hook, $version, $replacement, $message );
	}
}


/**
 * Check if child theme is active
 * 
 * Using this instead of is_child_theme — https://developer.wordpress.org/reference/functions/is_child_theme/#comment-3209
 * 
 * @since 	0.2.7
 *
 * @return 	bool 	True, if child theme is active
 */
function fx_bam_child_theme_is_active(): bool {
	$child_dir	= get_stylesheet_directory();
	$parent_dir	= get_template_directory();
	
	return ( $child_dir !== $parent_dir );
}


/**
 * Check for (and retrieve) child theme file based on provided file
 * 
 * @internal 	This function will confirm if a child file actually exists, but it's up to the calling function to 
 * 				confirm that the returning file actually exists
 *
 * @since	0.2.7
 *
 * @param	string	$file_path 	File path to check 
 * @return 	string 				Path for child file (if child file exists) or original file
 */
function fx_bam_maybe_get_child_theme_file_path( string $file_path ): string {

	// is file actually in a theme?
	if( false === strpos( $file_path, get_theme_root() ) ) {
		return $file_path;
	}

	// if no child theme, stop here
	if( !fx_bam_child_theme_is_active() ) {
		return $file_path;
	}

	// guess child file path
	$child_file_path = str_replace(
		get_template_directory(),
		get_stylesheet_directory(),
		$file_path
	);

	// is it actually a file?
	if( is_file( $child_file_path ) ) {
		$file_path = $child_file_path;
	}

	return $file_path;
}


/**
 * Recursively search through array for target value(s)
 * 
 * @since 	0.2.13
 *
 * @param	array   $arg    Array to search
 * @param   mixed  	$target Either target string or array of target strings
 * @param 	bool 	$exact 	True, for exact match, false if just checking if value contains target
 * 
 * @return  bool            True, if target was found
 */
function fx_bam_array_contains_value( array $arg, $target = [], bool $exact = false ): bool {
	$target = (array)$target;

	foreach( $arg as $key => $value ) {
		if( is_array( $value ) ) {
			if( fx_bam_array_contains_value( $value, $target, $exact ) ) {
				return true;
			}

		} else {
			foreach( $target as $needle ) {
				if( $exact ) {
					if( $key === $needle || $value === $needle ) {
						return true;
					}
				} else {
					if( false !== strpos( $key, $needle ) || false !== strpos( $value, $needle ) ) {
						return true;
					}
				}
			}
		}
	}

	return false;
}



/**
 * Confirm arg is a WP_Post or try to get WP_Post from argument
 * 
 * @since 	0.2.13
 *
 * @param	mixed 	$arg 	Post arg
 * @return 	mixed 			WP_Post, if valid post; otherwise, false
 */
function fx_bam_ensure_wp_post( $arg ) {
	if( empty( $arg ) ) {
		$arg = get_the_ID();
	}

	if( is_numeric( $arg ) ) {
		$arg = get_post( absint( $arg ) );
	}

	return is_a( $arg, 'WP_Post' ) ? $arg : false;
}
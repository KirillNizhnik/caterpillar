<?php


use MatthiasMullie\Minify;



defined( 'ABSPATH' ) || exit;



final class FX_Asset_Minifier
{
    protected static $instance = null;

	private $loaded_dependencies = false;

	
    /**
     * Static Singleton Factory Method
     * @return self returns a single instance of our class
     */
    public static function instance() {
        if( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    private function load_dependencies( )
    {
		if( !$this->loaded_dependencies ) {
			require_once( FX_Block_Asset_Manager()::$plugin_path . '/vendor/autoload.php' );

			$this->loaded_dependencies = true;
		}

	}
	  
	
	/**
	 * Check that case is valid
	 *
	 * @param string  $case   Case
	 * @return  mixed       Exception on invalid case
	 */
	private function check_case( string $case )
	{
    	return in_array( $case, [ 'css', 'js' ] );
	}
	

	/**
	 * Minify file based on asset type
	 *
	 * @param	string	$file_path 	Asset file path
	 * @param 	string 	$case 		Minifier case (i.e. either "css" or "js")
	 * 
	 * @return	string 				Minified content
	 */
	public function minify_file( string $file_path, string $case )
	{
		$this->load_dependencies();

		$valid_case = $this->check_case( $case );
		if( !$valid_case ) {
			return false;
		}

		if( is_file( $file_path ) ) {
			return false;
		}

		if( 'css' === $case ) {
			$minifier = new Minify\CSS( $file_path );
		} elseif( 'js' === $case ) {
			$minifier = new Minify\JS( $file_path );
		}

		return $minifier->minify();
	}
    

	/**
	 * Minify content based on asset type
	 *
	 * @param	string	$content 	Asset content
	 * @param 	string 	$case 		Minifier case (i.e. either "css" or "js")
	 * 
	 * @return 	string 				Minified content
	 */
	public function minify_content( string $content, string $case )
	{
		$this->load_dependencies();

		$valid_case = $this->check_case( $case );
		if( !$valid_case ) {
			return false;
		}

		if( 'css' === $case ) {
			$minifier = new Minify\CSS();
		} elseif( 'js' === $case ) {
			$minifier = new Minify\JS();
		}	
		
		$minifier->add( $content );

		return $minifier->minify();
	}

}



function FX_Asset_Minifier() {
    return FX_Asset_Minifier::instance();
}
<?php
namespace Cat\Controllers;

class Progress
{
    public static $log = false;

	protected static $instance;
	
	public static $cli_progress;

	/**
	 * Initializes plugin variables and sets up WordPress hooks/actions.
	 *
	 * @return void
	 */

	protected function __construct()
	{
		add_action('wp_ajax_cat_progress_poll', array($this, 'result') );
		add_action('wp_ajax_nopriv_cat_progress_poll', array($this, 'result') );
	}

	/**
	 * Singleton factory Method
	 * Forces that only on instance of the class exists
	 *
	 * @return $instance Object, Returns the current instance or a new instance of the class
	 */

	public static function instance()
	{
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}

	public function result()
	{
		echo json_encode( self::get() );
		exit;
	}

	public static function get()
	{
		$indexed = get_transient('cat_progress_indexed');
		$total   = get_transient('cat_progress_total');
		$text    = get_transient('cat_progress_text');

        return array(
            'indexed' => $indexed
            ,'total'  => $total
            ,'text'   => $text
        );
	}

    /**
     * Log out the progresss if enabled
     */
    protected static function log()
    {
        if (self::$log)
        {
            $data = self::get();
            $message = "Progress: " . $data['text'] . " - " . $data['indexed'] . "/" . $data['total'];
            
            CAT_Log($message);
            
        }
    }


	/**
	 * Setup temporary cache data
	 * used by heartbeat api to update progress.
	 */

	public static function set($interval=null, $total=null, $text='')
	{
		if($interval !== null && $interval !== false) {
            set_transient( 'cat_progress_indexed', $interval, 30*MINUTE_IN_SECONDS );
            
		}
		
		if($total !== null && $total !== false) {
        	set_transient( 'cat_progress_total', $total, 30*MINUTE_IN_SECONDS );
        	if(CAT()->is_cli()) {
                self::$cli_progress = \WP_CLI\Utils\make_progress_bar($text, $total);
        	}
		}
		
        if(!empty($text)) {
        	set_transient( 'cat_progress_text', $text, 30*MINUTE_IN_SECONDS );
        	
        	
        }

        self::log();
	}


	/**
	 * set a specific field
	 * @param string $field transient short name
	 * @param sting|int $value the value for the transient
	 */
	public static function update($field, $value)
	{
		switch($field)
		{
			case 'index':
				set_transient( 'cat_progress_indexed', $value, 30*MINUTE_IN_SECONDS );
				if(CAT()->is_cli()) {
				   self::$cli_progress->tick();
				    if($value == get_transient( 'cat_progress_total')) {
				        self::$cli_progress->finish();
				    }
				}
				break;
			case 'total':
				set_transient( 'cat_progress_total', $value, 30*MINUTE_IN_SECONDS );
				
				break;
			case 'text':
				set_transient( 'cat_progress_text', $value, 30*MINUTE_IN_SECONDS );
			
				break;
			default:
				set_transient( 'cat_progress_'.$field, $value, 30*MINUTE_IN_SECONDS );
				
				break;
		}
        self::log();
	}
}

Progress::instance();

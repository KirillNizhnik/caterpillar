<?php
namespace Cat\Helpers;

class Logger
{

	protected static $instance;

    public $log = true;
    public $echo = false;
    public $shell = false;

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

    /**
     * Write/log data based on instance settings
     * @param  string|array $data to log
     * @return void
     */
    public function write( $data)
    {
        $lines = array();

        if ( is_array( $data ) || is_object( $data ) ) {
            $lines[]= 'CAT: ' . gettype($data);
            $lines[]= print_r($data, true);
        } else {
            $lines[]= 'CAT: '. $data;
        }

        if ($this->log)
            $this->_log($lines);

        if ($this->echo)
            $this->_echo($lines);

    }

    /**
     * log information to the debug log
     * @param  array $lines to log
     * @return void
     */
    public function _log( $lines )
    {
        if ( true === WP_DEBUG )
            foreach ($lines as $line)
                error_log($line);
    }

    /**
     * Output information to browser or shell
     * @param  array $lines to echo
     * @return void
     */
    public function _echo( $lines )
    {
        $eol = $this->shell ? "\n" : "<br>";
        $prefix = date('Y-m-d H:i:s') . ' : ';
        foreach ($lines as $line)
            echo $prefix . $line . $eol;
    }

}

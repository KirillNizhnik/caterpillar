<?php
namespace Cat\Controllers\Admin;

/**
 * Alert
 * Shows alerts and prvides interfaces for adding/removing them
 *
 * @Package CAT New Feed/Admin
 * @category admin
 * @author WebpageFX
 */

if ( ! defined('ABSPATH') )
	exit;


class Alert
{
	protected static $instance;
    protected static $alerts;

	/**
	 * Initializes variables and sets up WordPress hooks/actions.
	 *
	 * @return void
	 */

	protected function __construct()
	{
        add_action( 'admin_notices', array('Cat\Controllers\Admin\Alert', 'render') );
	}

	/* Static Singleton Factory Method */
	public static function instance()
	{
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}

    /**
     * Render alerts as html
     **/
    public static function render()
    {
        $alerts = self::get();
        $remove_ids = array();
        foreach ($alerts as $id => $alert)
        {
            $content = $alert['content'];
            $class = $alert['class'];
            $once = $alert['once'];

            if ($once)
                $remove_ids[]= $id;

?>
    <div class="<?php echo $class ?>">
        <p><?php echo $content ?></p>
    </div>
<?php
        }

        self::remove($remove_ids);
    }

    /**
     * Add a new alert
     * @content content of alert, html to go in p tag
     * @class notice, updated, error, update-nag
     * @once whether to delete the alert after showing it once
     * @id optionally specify the id - eg. in case it should be referenced in the message.  Will overwrite existing id.
     **/
    public static function add($content, $class="updated", $once=true, $id=null)
    {
        $alerts = self::get();
        // get time with microseconds to use as alert identifier
        if (empty($id))
        {
            $id = microtime(true);
            while(isset($alerts[$id]))
                $id++;
        }
        $alerts[$id] = array(
            'content' => (String) $content
            ,'class' => (String) $class
            ,'once' => (Boolean) $once
        );
        self::set($alerts);

        // return the self-generated id for reference
        return $id;
    }

    public static function remove($ids)
    {
        if (!is_array($ids))
            $ids = array($ids);

        $alerts = self::get();

        foreach ($ids as $id)
            unset($alerts[$id]);

        self::set($alerts);
    }

    /**
     * Get all alerts
     **/
    protected static function get()
    {
        if (is_null(self::$alerts))
        {
            $alerts = get_option('cat_alert_data', '');
            $alerts = maybe_unserialize($alerts);
            if (!is_array($alerts) or empty($alerts))
                $alerts = array();

            self::$alerts = $alerts;
        }

        return self::$alerts;
    }

    /**
     * Set all alerts
     */
    protected static function set($alerts)
    {
        self::$alerts = $alerts;
        $alerts = maybe_serialize($alerts);
        update_option('cat_alert_data', $alerts);
    }
}

Alert::instance();

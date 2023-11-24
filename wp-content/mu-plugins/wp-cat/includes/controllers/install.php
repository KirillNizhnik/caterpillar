<?php
namespace Cat\Controllers;
use Cat\Controllers\Admin\Alert;

// Don't load directly
if ( !defined('ABSPATH') ) die;

class Install
{
	protected static $instance;


    /**
	 * Initializes plugin variables and sets up WordPress hooks/actions.
	 *
	 * @return void
	 */

	protected function __construct( )
	{
        register_activation_hook( CAT_PLUGIN_FILE, array( $this, 'install' ));
        register_deactivation_hook( CAT_PLUGIN_FILE, array( $this, 'uninstall' ));

        add_action( 'admin_init', array( $this, 'check_version' ), 5 );
        add_action( 'admin_init', array( $this, 'maybe_install' ), 5 );
	}



	/**
     * Static Singleton Factory Method
     * @return [class] instance of the classe
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
     * Installs the database tables on activation
     * @return null
     */

	public function install($new = true)
	{
		require_once ABSPATH.'wp-admin/includes/upgrade.php';

        global $wpdb;

        CAT_Log('Creating Tables');
        $this->create_tables();

        CAT_Log('Updating DB Version');
        update_option('cat_db_version', CAT_VERSION);

        CAT_Log('Flushing Rewrite Rules');
        flush_rewrite_rules();

        CAT_Log('Unscheduling Crons');
        $this->unschedule_cron();

        CAT_Log('Re-Scheduling Crons');
        $this->schedule_cron();

        CAT_Log('Copying template files');
        $this->copy_template_files();

        CAT_Log('Install Complete');
        do_action( 'wp_cat_installed' );

        if ($new)
        {
            Alert::add(
                "The CAT Plugin (version " . CAT_VERSION . ") has been installed.  <a href='./options-general.php?page=cat_assistant'>Click here</a> to complete setup or hide this message."
                ,'update-nag'
                ,false
                ,'cat_install_prompt_setup'
            );
        }
        else
        {
            Alert::add("The CAT Plugin has been updated to version " . CAT_VERSION);
        }

	}



    /**
     * Uninstall the database tables on activation
     * @return null
     */

	public function uninstall()
	{
        $this->unschedule_cron();
        delete_option('cat_db_version');
        delete_option('cat_assistant_disabled');
	}


    /**
     * Maybe run installation ( for mu plugins integration)
     *
     * @return void
     */
    public function maybe_install()
    {
        $mu_dir = ABSPATH.MUPLUGINDIR.'/'.basename(__DIR__);
        $mu_installed = get_option('wp_cat_mu');


        if( ! $mu_installed )
        {
            $this->install();
            update_option('wp_cat_mu', true);
        }
    }


    /**
     * Runs any database updates that need run
     *
     * @return void
     */

    public function check_version()
    {
        $version = get_option('cat_db_version');

        if($version != CAT_VERSION)
        {
            CAT_Log('Version does not match - installing');
            $this->install(empty($version));
            do_action( 'wp_cat_updated' );
        }
    }

    /**
     * Copy template files  to active theme folder
     */
    public function copy_template_files()
    {
        $theme_folder = get_stylesheet_directory();
        $cat_folder = $theme_folder . "/cat";
        if (!is_dir($cat_folder))
        {
            $this->copy_dir(CAT()->plugin_path.'templates', $cat_folder, array('admin'));
        }
    }

    /**
     * Custom function to copy a directory, since WordPress filesystem
     * doesn't seem to load soon enough
     */
    protected function copy_dir($from, $to, $exclude=array())
    {
        $from = trailingslashit($from);
        $to = trailingslashit($to);

        if (!is_dir($from))
        {
            error_log("Source directory must exist in copy_dir");
            return false;
        }

        if (!is_dir($to))
            mkdir($to, 0755, 'recursive');

        $fromdir = opendir($from);
        while ($file = readdir($fromdir))
        {
            if ($file != "." and $file != ".." and !in_array($file, $exclude))
            {
                if (is_file($from.$file))
                    copy($from.$file, $to.$file);
                if (is_dir($from.$file))
                    $this->copy_dir($from.$file, $to.$file);
            }
        }
        closedir($fromdir);
    }


    /**
     * Creates the needed tables for the plugin
     * @return [type] [description]
     */
    private function create_tables()
    {
        global $wpdb;
   		$specs    = $wpdb->prefix . 'cat_product_specs';
   		$termmeta = $wpdb->prefix . 'cat_termmeta';
        $term_industries = $wpdb->prefix . 'cat_term_industries';
        $term_industries = $wpdb->prefix . 'cat_search_index';


   		$wpdb->hide_errors();

        $collate = '';

        if ( $wpdb->has_cap( 'collation' ) ) {
            if ( ! empty($wpdb->charset ) ) {
                $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
            }
            if ( ! empty($wpdb->collate ) ) {
                $collate .= " COLLATE $wpdb->collate";
            }
        }

   		$tables = "CREATE TABLE {$wpdb->prefix}cat_product_specs (
			id int(11) NOT NULL AUTO_INCREMENT,
			family_id int(11) NOT NULL,
			product_id int(11) NOT NULL,
			spec_group_id int(11) NOT NULL,
			spec_id int(11) NOT NULL,
			name varchar(255) NOT NULL,
			group_name varchar(255) NOT NULL,
			value_english varchar(255) NOT NULL,
			value_metric varchar(255) NOT NULL,
			unit_english varchar(255) NOT NULL,
			unit_metric varchar(255) NOT NULL,
			type varchar(255) NOT NULL,
			sort int(9) NOT NULL,
			sort_custom int(9) NOT NULL,
			group_sort int(9) NOT NULL,
			group_sort_custom int(9) NOT NULL,
			priority int(9) NOT NULL,
			created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			updated datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY  spec_id (family_id, product_id ,spec_id)
		) $collate;

		CREATE TABLE {$wpdb->prefix}cat_termmeta (
			meta_id bigint(20) NOT NULL auto_increment,
			cat_term_id bigint(20) NOT NULL,
			meta_key varchar(255) NULL,
			meta_value longtext NULL,
			PRIMARY KEY  (meta_id),
			KEY  cat_term_id (cat_term_id),
			KEY  meta_key (meta_key)
		) $collate;


        CREATE TABLE {$wpdb->prefix}cat_term_industries (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            industry_id bigint(20) NOT NULL,
            object_id bigint(20) NOT NULL,
            application_id bigint(20) NOT NULL,
            object_type varchar(255) NOT NULL,
            PRIMARY KEY  (id),
            KEY  industry_id (industry_id),
            KEY  object_id (object_id),
            KEY  application_id (application_id),
            KEY  object_type (object_type)
        ) $collate;


        CREATE TABLE {$wpdb->prefix}cat_search_index (
            id BIGINT unsigned not null auto_increment,
            post_id INT unsigned,
            param_name VARCHAR(255),
            param_source VARCHAR(255),
            param_value TEXT,
            param_display_value TEXT,
            PRIMARY KEY  (id),
            INDEX  post_id_idx (post_id),
            INDEX  param_name_idx (param_name),
            INDEX  param_source_idx (param_source)
        ) DEFAULT CHARSET=utf8";

		dbDelta( $tables );
    }


    private function schedule_cron()
    {
        $month = date('n');
        $tomorrow = date('j') + 1;

        $two_am = mktime(2, 0, 0, $month, $tomorrow);
        $three_am = mktime(3, 0, 0, $month, $tomorrow);

        wp_schedule_event( $two_am, 'daily', 'cat_rental_cron_import');
        wp_schedule_event( $three_am, 'daily', 'cat_used_cron_import');
    }


    // Called when settings change
    public static function update_scheduled_cron()
    {

        $all_classes = CAT()->get_available_classes();
        foreach ($all_classes as $id => $name)
        {
            CAT_Log('Clearing cron - cat_new_cron_import - '.$id . ' - '.$name);
            wp_clear_scheduled_hook('cat_new_cron_import', array((string)$id));
        }

        $classes = get_option('cat_new_class_limitation');

        $month = date('n');
        $tomorrow = date('j') + 1;

        if( is_array($classes) )
        {
            // Start at midnight, import 1 class every 15 minutes
            foreach($classes as $i => $id)
            {
                $total_minutes = ($i * 15);
                $minutes = $total_minutes % 60;
                $hours = ($total_minutes - $minutes) / 60;
                $time = mktime($hours, $minutes, 0, $month, $tomorrow);

                CAT_Log('Re-scheduling Cron - '.$id);
                wp_schedule_event( $time, 'daily', 'cat_new_cron_import', array($id));
            }
        }
    }


    private function unschedule_cron()
    {
        // Rental
        while ($next_schedule = wp_next_scheduled( 'cat_rental_cron_import' ))
        {
            CAT_Log($next_schedule);
            wp_unschedule_event($next_schedule, 'cat_rental_cron_import');
        }

        // Used
        while ($next_schedule = wp_next_scheduled( 'cat_used_cron_import' ))
        {
            CAT_Log($next_schedule);
            wp_unschedule_event($next_schedule, 'cat_used_cron_import');
        }
    }


}

Install::instance();

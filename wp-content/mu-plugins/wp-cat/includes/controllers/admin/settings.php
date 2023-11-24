<?php
namespace Cat\Controllers\Admin;

/**
 * CNF_Admin_Settings
 * handles setting up the settings pages
 *
 * @Package CAT New Feed/Admin
 * @category admin
 * @author WebpageFX
 */

if ( ! defined('ABSPATH') )
	exit;


class Settings extends \Cat\Core\Abstracts\Settings
{

	protected static $instance;

    private $options_key = 'cat-settings';
    private $setting_tabs = array(
                'general' => 'General'
                ,'feeds'  => 'Feeds'
                ,'importer' => 'Importer'
                ,'importer-categories' => 'Importer Categories'
                ,'new-equipment-rule' => 'New Equipment Rule'
                ,'used-equipment-rule' => 'Used Equipment Rule'
                ,'test-page' => 'Test Import'
            );
    private $rental_environments = array(
                'production' => 'Production'
                ,'qa' => 'QA'
            );


	/**
	 * Initializes variables and sets up WordPress hooks/actions.
	 *
	 * @return void
	 */

	protected function __construct( )
	{
		parent::__construct();
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
	 * Adds the menu item to
	 *
	 * @return void
	 */
	public function add_menu()
    {
        $page = add_submenu_page(
        	'options-general.php'
        	,'CAT'
        	,'CAT'
        	,'manage_options'
        	,$this->options_key
        	,array($this, 'load_page_template')
        );
    }



    public function load_page_template()
    {
        $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';

        if(isset($_GET['settings-updated']) AND $_GET['settings-updated']) {
            if ($tab == 'feeds') {
                echo '<div id="permalinksmessage" class="error">';
                echo '<p><strong>' . __( 'Permalinks Issue:', 'cat-new-feed' ) . '</strong> ' . sprintf( __( 'You must %sgo to your Permalink Settings%s and resave your permalink structure.', 'cat-new-feed' ), '<a href="' . esc_url( admin_url( 'options-permalink.php' ) ) . '">', '</a>' ).'</p></div>';

                echo '<div id="permalinksmessage" class="error">';
                echo '<p><strong>' . __( 'Rental Changes:', 'cat-new-feed' ) . '</strong> ' . sprintf( __( 'If rental environment has changed or enabled/disabled, it is recommended that you Purge, then Import Rental Data under the "Importer" tab', 'cat-new-feed' )).'</p></div>';

                \Cat\Controllers\Install::update_scheduled_cron();
		/*$month = date('n');
		$tomorrow = date('j') + 1;

	        $two_am = mktime(2, 0, 0, $month, $tomorrow);
        	$three_am = mktime(3, 0, 0, $month, $tomorrow);

	        wp_schedule_event( $two_am, 'daily', 'cat_rental_cron_import');
        	wp_schedule_event( $three_am, 'daily', 'cat_used_cron_import');*/
            }
        }


        $classes = get_option('cat_new_class_limitation');
        $classes = is_array($classes) ? $classes : array();

        $available_classes        = CAT()->get_available_classes();
        $class_post_type_relation = CAT()->get_class_post_type_relation();
    ?>

    	<div class="wrap">
            <h2>CAT Settings</h2>
    		<?php $this->tabs(); ?>

	        <?php if( $tab !== 'importer' ): ?>
            <form method="post" action="options.php">
            <?php settings_fields( 'cat_settings' ); ?>
	    	<?php endif; ?>

            <?php include_once CAT()->plugin_path.'templates/admin/settings/'.$tab.'.php'; ?>

            <?php if( $tab !== 'importer' ): ?>
            <?php submit_button(); ?>
			</form>
           <?php endif; ?>

        </div>

    <?php
    }


    /**
     * Renders our settings tabs
     * @return [type] [description]
     */
    private function tabs()
    {
        $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';

        echo '<h2 class="nav-tab-wrapper">';
        foreach ( $this->setting_tabs as $tab_key => $tab_caption ) {
            $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
            echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
        }
        echo '</h2>';
    }



    public function add_settings_page()
    {
        $current_tab = isset( $_POST['tab'] ) ? $_POST['tab'] : 'general';

        /**
         * Temporary work around
         * TODO: Figure out why settings are not saving properly
         */
        if( ! empty($_POST) )
        {
            if( $current_tab == 'feeds' )
            {

                // New Feed
                register_setting( 'cat_settings', 'cat_new_sales_channel_code');
                register_setting( 'cat_settings', 'cat_new_secret_api_code');
                register_setting( 'cat_settings', 'cat_new_class_limitation');
                register_setting( 'cat_settings', 'cat_new_email_update');

                // Slugs
                register_setting( 'cat_settings', 'cat_new_machine_slug');
                register_setting( 'cat_settings', 'cat_new_attachment_slug');
                register_setting( 'cat_settings', 'cat_new_power_slug');
                register_setting( 'cat_settings', 'cat_new_allied_slug');

                register_setting( 'cat_settings', 'cat_new_machine_rental_slug');
                register_setting( 'cat_settings', 'cat_new_attachment_rental_slug');
                register_setting( 'cat_settings', 'cat_new_power_rental_slug');
                register_setting( 'cat_settings', 'cat_new_allied_rental_slug');
                register_setting( 'cat_settings', 'cat_new_allied_pwr_rental_slug');
                register_setting( 'cat_settings', 'cat_industry_rental_slug');

                register_setting( 'cat_settings', 'cat_industry_slug');

                // Rental API
                register_setting( 'cat_settings', 'cat_rental_environment');
                foreach ($this->rental_environments as $key => $name)
                {
                    register_setting( 'cat_settings', 'cat_rental_' . $key . '_user');
                    register_setting( 'cat_settings', 'cat_rental_' . $key . '_password');
                }

                // Used Feed
                register_setting( 'cat_settings', 'cat_used_feed_url');
                register_setting( 'cat_settings', 'cat_used_machine_slug');
                register_setting( 'cat_settings', 'cat_used_email_update');

            }
            else if ($current_tab == 'general')
            {
                register_setting( 'cat_settings', 'cat_use_industries');
                register_setting( 'cat_settings', 'cat_use_applications');
                register_setting( 'cat_settings', 'cat_financing_url');
                register_setting( 'cat_settings', 'cat_demo_url');
                register_setting( 'cat_settings', 'cat_em_solutions_url');
                register_setting( 'cat_settings', 'cat_rent_url');
                register_setting( 'cat_settings', 'archive_category');
                register_setting( 'cat_settings', 'removeFolderByLink');
            }
        }

    }


    /*
     * The following methods provide descriptions
     * for their respective sections, used as callbacks
     * with add_settings_section
     */
    function section_general_desc() { echo ''; }
    function section_new_desc() { echo 'Manage CAT Feed Options'; }

}

Settings::instance();

<?php
namespace Cat\Controllers\Admin;

/**
 * Assistant
 * Handles page and logic for setup assistant
 *
 * @Package CAT New Feed/Admin
 * @category admin
 * @author WebpageFX
 */

if ( ! defined('ABSPATH') )
	exit;


class Assistant extends \Cat\Core\Abstracts\Settings
{

	protected static $instance;

    private $options_key = 'cat_assistant';
    private $setting_tabs = array(
        'general'           => 'General'
        ,'new'              => 'New'
        ,'rental'           => 'Rental'
        ,'used'             => 'Used'
        ,'troubleshooting'  => 'Troubleshooting'
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
        // option set, but not just now
        $disabled = (empty($_POST[$this->options_key.'_disabled']) and get_option($this->options_key.'_disabled', false));
        if (!$disabled) {
            $page = add_submenu_page(
                'options-general.php'
                ,'CAT Setup Assistant'
                ,'CAT Setup Assistant'
                ,'manage_options'
                ,$this->options_key
                ,array($this, 'load_page_template')
            );
        }
    }



    public function load_page_template()
    {
        $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';

    ?>
    	<div class="wrap">
            <h2>CAT Setup Assistant</h2>
    		<?php $this->tabs(); ?>

            <form method="post" action="options.php">
                <?php settings_fields( $this->options_key ); ?>
                <?php include_once CAT()->plugin_path.'templates/admin/assistant/'.$tab.'.php'; ?>
			</form>

        </div>
    <?php
    }


    /**
     * Renders our assistant tabs
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
         */
        if( ! empty($_POST) )
        {
            if ($current_tab == 'general')
            {
                if (!empty($_POST[$this->options_key.'_disabled']))
                {
                    Alert::remove('cat_install_prompt_setup');
                    update_option($this->options_key.'_disabled', 1);
                    Alert::add('The CAT Setup Assistant has been disabled');
                    wp_redirect('/wp-admin/index.php');
                    exit;
                }
            }
        }
    }


    /*
     * The following methods provide descriptions
     * for their respective sections, used as callbacks
     * with add_assistant_section
     */
    function section_general_desc() { echo ''; }
    function section_new_desc() { echo 'CAT Plugin Setup Assistant'; }

}

Assistant::instance();

<?php namespace Fx_Cat_Helper\includes;

/**
 * Sets the admin tools subpage for the helper GUI, handles assets queues, and
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tools_Page {

	/**
	 * Class instance.
	 *
	 * @see  instance()
	 * @type object
	 */
	protected static $instance = null;

	/**
	 * Options key for wp admin page
	 *
	 * @var string
	 */
	private $options_key = 'cathelpertool';

	/**
	 *
	 * @var string[]
	 */
	private $setting_tabs = [
		'search-cpc-feed' => 'Search New/CPC',
		'search-dsf-feed' => 'Search Used/DSF',
		'cpc-xmls'        => 'New/CPC XML viewer',
		'plugin-health'   => 'Feed Health',

	];

	/**
	 * Plugin URL for asset use
	 *
	 * @var string|null
	 */
	public static $plugin_url = null;

	/**
	 * Initiate class and add hooks
	 *
	 * @return void
	 */
	protected function __construct() {
		self::$plugin_url = plugin_dir_url( FX_CAT_HELPER_PLUGIN_FILE );
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'asset_manager' ], 100 );
	}

	/**
	 * Instance of this class used later for global function
	 *
	 * @return object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Add tools sub page for gui
	 *
	 * @return void
	 */
	public function add_menu() {
		$plugin_tools_page = add_management_page(
			'CAT Helper Tool',
			'CAT Helper',
			'manage_options',
			$this->options_key,
			[
				$this,
				'admin_page',
			]
		);
		add_action( "load-$plugin_tools_page", [ $this, 'admin_page_load' ] );
	}

	/**
	 * Future ideas/reminder for later
	 *
	 * @return void
	 */
	public function admin_page_load() {
		// ... future
	}

	/**
	 * Setup main wrapper markup for tools page, and setup includes on our tabs
	 *
	 * @return void
	 */
	public function admin_page() {
		$tab = isset( $_GET['tab'] ) ? filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING ) : 'search-cpc-feed';
		?>
		<div class="wrap">
			<h2>WP CAT Helper Tool</h2>
			<?php
			settings_errors();
			$this->tabs();
			include plugin_dir_path( FX_CAT_HELPER_PLUGIN_FILE ) . 'templates/' . $tab . '.php';

			?>
		</div>
		<?php
	}

	/**
	 * Queue our assets based on tab key
	 *
	 * @return void
	 */
	public function asset_manager() {
	    if ( isset( $_GET['page'] ) && isset( $_GET['tab'] ) && $_GET['tab'] === 'plugin-health' ) {
			wp_enqueue_style( 'chp-datatables-css', 'https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css', [], null, 'all' ); //set to plugin version for filemtime
			wp_enqueue_script( 'chp-datatables-js', 'https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js', [], null, true );
		}
		if ( ( isset( $_GET['page'] ) && $_GET['page'] === 'cathelpertool' ) && ! isset( $_GET['tab'] ) ) { //failsafe logic for first tab
			$file_name = array_key_first( $this->setting_tabs ); // first array key in case changed later
			$js_path   = self::$plugin_url . 'assets/js/' . $file_name . '.js';
			$css_path  = self::$plugin_url . 'assets/css/' . $file_name . '.css';
			wp_enqueue_style( $file_name, $css_path, [], filemtime( $css_path ), 'all' );
			wp_enqueue_script( $file_name, $js_path, [], filemtime( $js_path ), true );
			wp_localize_script(
				$file_name,
				'Fx_Chp',
				[
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'random'   => 1234,
				]
			);
		} elseif ( isset( $_GET['page'] ) && isset( $_GET['tab'] ) ) {
			$file_name = $_GET['tab'];
			$js_path   = self::$plugin_url . 'assets/js/' . $file_name . '.js';
			$css_path  = self::$plugin_url . 'assets/css/' . $file_name . '.css';
			wp_enqueue_style( $file_name, $css_path, [], filemtime( $css_path ), 'all' );
			wp_enqueue_script( $file_name, $js_path, [], filemtime( $js_path ), true );
			wp_localize_script(
				$file_name,
				'Fx_Chp',
				[
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'random'   => 1234,
				]
			);
		}

	

	}


	/**
	 * Renders our settings tabs
	 * @return void [type] [description]
	 */
	private function tabs() {
		$current_tab = isset( $_GET['tab'] ) ? filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING ) : 'search-cpc-feed';
		?>
		<h2 class="nav-tab-wrapper">
			<?php
			foreach ( $this->setting_tabs as $tab_key => $tab_caption ) {
				$active = $current_tab === $tab_key ? 'nav-tab-active' : '';
				?>
			<a class="nav-tab <?php echo esc_html( $active ); ?>" href="?page=<?php echo esc_html( $this->options_key ); ?>&tab=<?php echo esc_html( $tab_key ); ?>"><?php echo esc_html( $tab_caption ); ?></a>
				<?php
			}
			?>
		</h2>
		<?php
	}


}

/**
 * Setup instance as a global function
 *
 * @return Tools_Page|object|null
 */
function fx_ch_register_tools_page() {
	return Tools_Page::instance();
}

//init class
fx_ch_register_tools_page();

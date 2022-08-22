<?php
/**
 * The file that defines the core plugin class
 */
class Sendy_Fulfillment {
	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'sendy-fulfillment';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sendy-fulfillment-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sendy-fulfillment-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sendy-fulfillment-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-sendy-fulfillment-public.php';

		$this->loader = new Sendy_Fulfillment_Loader();

	}

	private function set_locale() {

		$plugin_i18n = new Sendy_Fulfillment_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	private function define_admin_hooks() {

		$plugin_admin = new Sendy_Fulfillment_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );


    }

	private function define_public_hooks() {

		$plugin_public = new Sendy_Fulfillment_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

}

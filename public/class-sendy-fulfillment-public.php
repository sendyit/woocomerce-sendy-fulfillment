<?php
/**
 * The public-facing functionality of the plugin.
 */
class Sendy_Fulfillment_Public {
	private $plugin_name;

	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sendy-fulfillment-public.css', array(), $this->version, 'all' );

	}
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cookie.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sendy-fulfillment-public.js', array( 'jquery' ), $this->version, false );

	}

}

<?php
namespace ElementorHubSpotBulb;

/**
 * Class Plugin
 *
 * Main Plugin class
 * @since 1.0.0
 */
class Plugin {
  /**
   * Instance
   *
   * @since 1.0.0
   * @access private
   * @static
   *
   * @var Plugin The single instance of the class.
   */
  private static $_instance = null;

  /**
   * Instance
   *
   * Ensures only one instance of the class is loaded or can be loaded.
   *
   * @since 1.0.0
   * @access public
   *
   * @return Plugin An instance of the class.
   */
  public static function instance() {
    if ( is_null( self::$_instance ) ) {
      self::$_instance = new self();
    }

    return self::$_instance;
  }

  /**
   * widget_scripts
   *
   * Load required plugin core files.
   *
   * @since 1.0.0
   * @access public
   */
  public function widget_scripts() {
//    wp_register_script( 'elementor-', plugins_url( '/assets/js/awesomesauce.js', __FILE__ ), [ 'jquery' ], false, true );
  }

  /**
   * Register Widgets
   *
   * Register new Elementor widgets.
   *
   * @since 1.0.0
   * @access public
   */
  public function register_widgets() {
    $widgets = require_once __DIR__ . "/config.php";

    // We check if the Elementor plugin has been installed / activated.
    if (defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base')) {

      // We look for any theme overrides for this custom Elementor element.
      // If no theme overrides are found we use the default one in this plugin.
      foreach ($widgets as $widget){
        require_once __DIR__ . "/widgets/{$widget['file']}";
        $class_name = __NAMESPACE__ . "\Widgets" . "\\{$widget['class']}";
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new $class_name() );
      }
    }
  }

  /**
   * Add Category
   *
   * @param $elements_manager
   */
  public function add_elementor_widget_categories( $elements_manager ) {
    $elements_manager->add_category(
      'hubspot-bulb',
      [
        'title' => __( 'HubSpot Bulb', 'elementor-hubspot-bulb' ),
        'icon' => 'fa fa-plug',
      ]
    );
  }

  /**
   *  Plugin class constructor
   *
   * Register plugin action hooks and filters
   *
   * @since 1.0.0
   * @access public
   */
  public function __construct() {
    // Register widget scripts
//    add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );

    // Register widget category
    add_action( 'elementor/elements/categories_registered', [ $this, 'add_elementor_widget_categories' ] );

    // Register widgets
    add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );
  }
}

// Instantiate Plugin Class
Plugin::instance();

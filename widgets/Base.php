<?php
namespace ElementorHubSpotBulb\Widgets;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Base Widget.
 *
 * @since 1.0.0
 */
class Base extends \Elementor\Widget_Base {
  /**
   * Get widget name.
   *
   * Retrieve widget name.
   *
   * @since 1.0.0
   * @access public
   *
   * @return string Widget name.
   */
  public function get_name() {}

  /**
   * Get widget title.
   *
   * Retrieve widget title.
   *
   * @since 1.0.0
   * @access public
   *
   * @return string Widget title.
   */
  public function get_title() {}

  /**
   * Get widget icon.
   *
   * Retrieve widget icon.
   *
   * @since 1.0.0
   * @access public
   *
   * @return string Widget icon.
   */
  public function get_icon() {
    return 'fa fa-code';
  }

  /**
   * Get widget categories.
   *
   * Retrieve the list of categories the oEmbed widget belongs to.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array Widget categories.
   */
  public function get_categories() {
    return [ 'hubspot-bulb', 'basic' ];
  }

  /**
   * Register widget controls.
   *
   * Adds different input fields to allow the user to change and customize the widget settings.
   *
   * @since 1.0.0
   * @access protected
   */
  protected function _register_controls() {}

  /**
   * Render widget output on the frontend.
   *
   * Written in PHP and used to generate the final HTML.
   *
   * @since 1.0.0
   * @access protected
   */
  protected function render() {}

  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );

//    animation style
    wp_enqueue_style(
      'elementor-hubspot-bulb-animation',
      PLUGIN_URL . '/assets/css/animation.css',
      false,
      PLUGIN_VERSION
    );
  }
}

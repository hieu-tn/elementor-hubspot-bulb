<?php
namespace ElementorHubSpotBulb\Widgets;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use \ElementorHubSpotBulb\Widgets\Base as Base;
use \IntlDateFormatter;
use \DateTime;

/**
 * Elementor oEmbed Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class HubSpot_Post_Grid_Widget extends Base {
  public function get_name() {
    return 'hubspot-post-grid';
  }

  public function get_title() {
    return __( 'HubSpot Post Grid', 'elementor-hubspot-bulb' );
  }

  protected function render() {
    $settings = $this->get_settings_for_display();
    $posts = $this->get_posts([
      'hapikey' => $settings['hapikey'],
      'posts_per_page' => $settings['posts_per_page'],
      'offset' => $settings['offset'],
      'content_group_id' => $settings['content_group_id'],
    ]);

    if( count($posts) ) :
    ?>
    <div class="hubspot-post-grid-container <?php echo $settings['number_of_columns'] ?>">
      <div class="post-grid-row">
        <?php foreach ($posts as $post) : ?>
        <article class="post-grid">
          <div class="post-grid-holder">
            <div class="entry-thumbnail">
              <div class="entry-overlay <?php echo $settings['post_grid_hover_animation'] ?>">
                <i class="<?php echo $settings['post_grid_hover_icon']['value'] ?>"></i>
                <a href="<?php echo $post->published_url ?>"></a>
              </div>
              <?php if ($settings['show_thumbnail']) : ?>
                <div class="entry-thumbnail__inner">
                  <img src="<?php echo $post->featured_image ?>" alt="<?php echo $post->featured_image_alt_text ?>" />
                </div>
              <?php endif; ?>
            </div>
            <div class="entry-wrapper">
              <?php if ($settings['show_title']) : ?>
                <div class="entry-header">
                  <a href="<?php echo $post->published_url ?>"><h2 class="entry-title"><?php echo $post->html_title ?></h2></a>
                  <?php if($settings['show_meta'] && $settings['meta_position'] == 'meta-entry-header') : ?>
                    <div class="entry-meta">
                      by
                      <span class="posted-by"><?php echo $post->blog_author->display_name ?></span>
                      on
                      <span class="posted-on"><time datetime="<?php echo $post->publish_date ?>"><?php echo date( 'd F, Y', $post->publish_date / 1000 ) ?></time></span>
                    </div>
                  <?php endif ?>
                </div>
              <?php endif ?>
              <div class="entry-content">
                <div class="entry-excerpt">
                  <?php if ($settings['show_excerpt']) echo strip_tags($post->meta->post_summary) ?>
                </div>
                <?php if($settings['show_meta'] && $settings['meta_position'] == 'meta-entry-footer') : ?>
                  <div class="entry-meta">
                    by
                    <span class="posted-by"><?php echo $post->blog_author->display_name ?></span>
                    on
                    <span class="posted-on"><time datetime="<?php echo $post->publish_date ?>"><?php echo date( 'd F, Y', $post->publish_date / 1000 ) ?></time></span>
                  </div>
                <?php endif ?>
                <?php if ($settings['show_read_more_button']) : ?>
                  <div class="read-more">
                    <a href="<?php echo $post->published_url ?>"><?php echo $settings['read_more_button_text'] ?></a>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
    <?php
    endif;
  }

  /**
   * get posts from HubSpot
   *
   * @param $args
   * @return string
   */
  public function get_posts($args) {
    // create a new cURL resource
    $ch = curl_init();
    // set URL and other appropriate options
    curl_setopt( $ch, CURLOPT_URL, "https://api.hubapi.com/content/api/v2/blog-posts?hapikey={$args['hapikey']}&limit={$args['posts_per_page']}&offset={$args['offset']}&content_group_id={$args['content_group_id']}&state=PUBLISHED" );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
    curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "GET" );

    // grab URL and pass it to the browser
    $response = curl_exec($ch);
    $err = curl_error($ch);

    if( $err ) return "cURL Error #:" . $err;

    return json_decode($response)->objects;
  }

  protected function _register_controls() {
    $this->_register_query_controls();
    $this->_register_layout_settings_controls();

    $this->_register_post_grid_style();
    $this->_register_color_typography();
    $this->_register_hover_style();
  }

  /**
   * Query Controls!
   */
  private function _register_query_controls() {
    $this->start_controls_section(
      'query_section',
      [
        'label' => __( 'Query', 'elementor-hubspot-bulb' ),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'content_group_id',
      [
        'label' => __( 'Content Group ID', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::TEXT,
        'placeholder' => __( 'Content Group ID', 'elementor-hubspot-bulb' ),
      ]
    );

    $this->add_control(
      'hapikey',
      [
        'label' => __( 'hapikey', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::TEXT,
        'placeholder' => __( 'hapikey', 'elementor-hubspot-bulb' ),
      ]
    );

    $this->add_control(
      'posts_per_page',
      [
        'label' => __( 'Posts Per Page', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'default' => '4',
      ]
    );

    $this->add_control(
      'offset',
      [
        'label' => __( 'Offset', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'default' => '0',
      ]
    );

    $this->end_controls_section();
  }

  /**
   * Layout Controls!
   */
  private function _register_layout_settings_controls() {
    $this->start_controls_section(
      'layout_setttings_section',
      [
        'label' => __( 'Layout Settings', 'elementor-hubspot-bulb' ),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'number_of_columns',
      [
        'label' => esc_html__( 'Number of Columns', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'gird-4',
        'options' => [
          'gird-1' => esc_html__( 'Single Column', 'elementor-hubspot-bulb' ),
          'gird-2' => esc_html__( 'Two Columns', 'elementor-hubspot-bulb' ),
          'gird-3' => esc_html__( 'Three Columns', 'elementor-hubspot-bulb' ),
          'gird-4' => esc_html__( 'Four Columns', 'elementor-hubspot-bulb' ),
        ],
      ]
    );

    $this->add_control(
      'show_thumbnail',
      [
        'label' => __( 'Show Thumbnail', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::CHOOSE,
        'options' => [
          '1' => [
            'title' => __( 'Yes', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-check',
          ],
          '0' => [
            'title' => __( 'No', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-ban',
          ],
        ],
        'default' => '1',
      ]
    );

    $this->add_control(
      'show_title',
      [
        'label' => __( 'Show Title', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::CHOOSE,
        'options' => [
          '1' => [
            'title' => __( 'Yes', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-check',
          ],
          '0' => [
            'title' => __( 'No', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-ban',
          ],
        ],
        'default' => '1',
      ]
    );

    $this->add_control(
      'show_excerpt',
      [
        'label' => __( 'Show excerpt', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::CHOOSE,
        'options' => [
          '1' => [
            'title' => __( 'Yes', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-check',
          ],
          '0' => [
            'title' => __( 'No', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-ban',
          ],
        ],
        'default' => '1',
      ]
    );

    $this->add_control(
      'show_read_more_button',
      [
        'label' => __( 'Show Read More Button', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::CHOOSE,
        'options' => [
          '1' => [
            'title' => __( 'Yes', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-check',
          ],
          '0' => [
            'title' => __( 'No', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-ban',
          ],
        ],
        'default' => '1',
      ]
    );

    $this->add_control(
      'read_more_button_text',
      [
        'label' => __( 'Button Text', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => __( 'Read More', 'elementor-hubspot-bulb' ),
        'condition' => [
          'show_read_more_button' => '1',
        ],
      ]
    );

    $this->add_control(
      'show_meta',
      [
        'label' => __( 'Show Meta', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::CHOOSE,
        'options' => [
          '1' => [
            'title' => __( 'Yes', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-check',
          ],
          '0' => [
            'title' => __( 'No', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-ban',
          ],
        ],
        'default' => '1',
      ]
    );

    $this->add_control(
      'meta_position',
      [
        'label' => esc_html__( 'Meta Position', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'meta-entry-footer',
        'options' => [
          'meta-entry-header' => esc_html__( 'Entry Header', 'elementor-hubspot-bulb' ),
          'meta-entry-footer' => esc_html__( 'Entry Footer', 'elementor-hubspot-bulb' ),
        ],
        'condition' => [
          'show_meta' => '1',
        ],
      ]
    );

    $this->end_controls_section();
  }

  /**
   * Grid Style Controls!
   */
  private function _register_post_grid_style() {
    $this->start_controls_section(
      'post_grid_style_section',
      [
        'label' => __( 'Post Grid Style', 'elementor-hubspot-bulb' ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE
      ]
    );

    $this->add_control(
      'post_grid_bg_color',
      [
        'label' => __( 'Post Background Color', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default' => '#fff',
        'selectors' => [
          '{{WRAPPER}} .grid-post-holder' => 'background-color: {{VALUE}}',
        ]

      ]
    );

    $this->add_responsive_control(
      'post_grid_spacing',
      [
        'label' => esc_html__( 'Spacing Between Items', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%', 'em' ],
        'selectors' => [
          '{{WRAPPER}} .grid-post' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Border::get_type(),
      [
        'name' => 'post_grid_border',
        'label' => esc_html__( 'Border', 'elementor-hubspot-bulb' ),
        'selector' => '{{WRAPPER}} .grid-post-holder',
      ]
    );

    $this->add_control(
      'post_grid_border_radius',
      [
        'label' => esc_html__( 'Border Radius', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::DIMENSIONS,
        'selectors' => [
          '{{WRAPPER}} .grid-post-holder' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Box_Shadow::get_type(),
      [
        'name' => 'post_grid_box_shadow',
        'selector' => '{{WRAPPER}} .grid-post-holder',
      ]
    );

    $this->end_controls_section();
  }

  /**
   * Color & Typography Controls!
   */
  private function _register_color_typography() {
    $this->start_controls_section(
      'color_typography_section',
      [
        'label' => __( 'Color & Typography', 'elementor-hubspot-bulb' ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE
      ]
    );

    $this->add_control(
      'post_grid_title_style',
      [
        'label' => __( 'Title Style', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'post_grid_title_color',
      [
        'label' => __( 'Title Color', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default'=> '#303133',
        'selectors' => [
          '{{WRAPPER}} .entry-header h2' => 'color: {{VALUE}};',
        ]

      ]
    );

    $this->add_control(
      'post_grid_title_hover_color',
      [
        'label' => __( 'Title Hover Color', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default'=> '#23527c',
        'selectors' => [
          '{{WRAPPER}} .entry-header:hover h2, {{WRAPPER}} .entry-header a:hover h2' => 'color: {{VALUE}};',
        ]

      ]
    );

    $this->add_responsive_control(
      'post_grid_title_alignment',
      [
        'label' => __( 'Title Alignment', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => __( 'Left', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-align-left',
          ],
          'center' => [
            'title' => __( 'Center', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-align-center',
          ],
          'right' => [
            'title' => __( 'Right', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-align-right',
          ]
        ],
        'selectors' => [
          '{{WRAPPER}} .entry-title' => 'text-align: {{VALUE}};',
        ]
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'eael_post_grid_title_typography',
        'label' => __( 'Typography', 'elementor-hubspot-bulb' ),
        'scheme' => \Elementor\Scheme_Typography::TYPOGRAPHY_1,
        'selector' => '{{WRAPPER}} .entry-title',
      ]
    );

    $this->add_control(
      'post_grid_excerpt_style',
      [
        'label' => __( 'Excerpt Style', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'post_grid_excerpt_color',
      [
        'label' => __( 'Excerpt Color', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default'=> '',
        'selectors' => [
          '{{WRAPPER}} .entry-excerpt, .entry-excerpt p' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_responsive_control(
      'post_grid_excerpt_alignment',
      [
        'label' => __( 'Excerpt Alignment', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => __( 'Left', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-align-left',
          ],
          'center' => [
            'title' => __( 'Center', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-align-center',
          ],
          'right' => [
            'title' => __( 'Right', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-align-right',
          ],
          'justify' => [
            'title' => __( 'Justified', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-align-justify',
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .entry-excerpt p' => 'text-align: {{VALUE}};',
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'post_grid_excerpt_typography',
        'label' => __( 'Excerpt Typography', 'elementor-hubspot-bulb' ),
        'scheme' => \Elementor\Scheme_Typography::TYPOGRAPHY_3,
        'selector' => '{{WRAPPER}} .entry-excerpt, .entry-excerpt p',
      ]
    );


    $this->add_control(
      'post_grid_meta_style',
      [
        'label' => __( 'Meta Style', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'post_grid_meta_color',
      [
        'label' => __( 'Meta Color', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default'=> '',
        'selectors' => [
          '{{WRAPPER}} .entry-meta, .entry-meta a, .entry-meta span' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_responsive_control(
      'post_grid_meta_alignment',
      [
        'label' => __( 'Meta Alignment', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::CHOOSE,
        'options' => [
          'flex-start' => [
            'title' => __( 'Left', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-align-left',
          ],
          'center' => [
            'title' => __( 'Center', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-align-center',
          ],
          'flex-end' => [
            'title' => __( 'Right', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-align-right',
          ],
          'stretch' => [
            'title' => __( 'Justified', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-align-justify',
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .entry-footer' => 'justify-content: {{VALUE}};',
          '{{WRAPPER}} .entry-meta'	=> 'justify-content: {{VALUE}};'
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name'		=> 'post_grid_meta_typography',
        'label'		=> __( 'Meta Typography', 'elementor-hubspot-bulb' ),
        'scheme'	=> \Elementor\Scheme_Typography::TYPOGRAPHY_3,
        'selector'	=> '{{WRAPPER}} .entry-meta, {{WRAPPER}} .entry-meta > div, {{WRAPPER}} .entry-meta > span',
      ]
    );

    $this->add_control(
      'post_grid_read_more_style',
      [
        'label' => __( 'Read more Style', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'post_grid_read_more_color',
      [
        'label' => __( 'Read more Color', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default'=> '',
        'selectors' => [
          '{{WRAPPER}} .read-more, .read-more a' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_responsive_control(
      'post_grid_read_more_alignment',
      [
        'label' => __( 'Read more Alignment', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::CHOOSE,
        'options' => [
          'flex-start' => [
            'title' => __( 'Left', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-align-left',
          ],
          'center' => [
            'title' => __( 'Center', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-align-center',
          ],
          'flex-end' => [
            'title' => __( 'Right', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-align-right',
          ],
          'stretch' => [
            'title' => __( 'Justified', 'elementor-hubspot-bulb' ),
            'icon' => 'fa fa-align-justify',
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .read-more' => 'text-align: {{VALUE}};',
          '{{WRAPPER}} .read-more a'	=> 'text-align: {{VALUE}};'
        ],
      ]
    );

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name'		=> 'post_grid_read_more_typography',
        'label'		=> __( 'Read more Typography', 'elementor-hubspot-bulb' ),
        'scheme'	=> \Elementor\Scheme_Typography::TYPOGRAPHY_3,
        'selector'	=> '{{WRAPPER}} .read-more > a, {{WRAPPER}} .read-more > span',
      ]
    );

    $this->end_controls_section();
  }

  /**
   * Hover Controls!
   */
  private function _register_hover_style() {
    $this->start_controls_section(
      'hover_card_styles_section',
      [
        'label' => __( 'Hover Card Style', 'elementor-hubspot-bulb' ),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE
      ]
    );

    $this->add_control(
      'post_grid_hover_animation',
      [
        'label' => esc_html__( 'Animation', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'fade-in',
        'options' => [
          'none'		=> esc_html__( 'None', 'elementor-hubspot-bulb' ),
          'hvr-fade'	=> esc_html__( 'FadeIn', 'elementor-hubspot-bulb' ),
//          'zoom-in'	=> esc_html__( 'ZoomIn', 'elementor-hubspot-bulb' ),
//          'slide-up'	=> esc_html__( 'SlideUp', 'elementor-hubspot-bulb' ),
        ],
      ]
    );

    $this->add_control(
      'post_grid_hover_icon',
      [
        'label'		=> __( 'Post Hover Icon', 'elementor-hubspot-bulb' ),
        'type'		=> \Elementor\Controls_Manager::ICONS,
        'fa4compatibility' => 'post_grid_bg_hover_icon',
        'default'	=> [
          'value' => 'fas fa-eye',
          'library' => 'fa-solid',
        ],
        'condition'	=> [
          'post_grid_hover_animation!'	=> 'none'
        ]
      ]
    );

    $this->add_control(
      'post_grid_hover_bg_color',
      [
        'label' => __( 'Background Color', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default' => 'rgba(0,0,0, .75)',
        'selectors' => [
          '{{WRAPPER}} .post-grid .entry-thumbnail:hover .entry-overlay' => 'background-color: {{VALUE}}',
        ]

      ]
    );

    $this->add_control(
      'post_grid_hover_icon_color',
      [
        'label' => __( 'Icon Color', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default' => '#ffffff',
        'selectors' => [
          '{{WRAPPER}} .post-grid .entry-overlay > i' => 'color: {{VALUE}}',
        ]

      ]
    );

    $this->add_responsive_control(
      'post_grid_hover_icon_fontsize',
      [
        'label' => __( 'Icon font size', 'elementor-hubspot-bulb' ),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'default' => [
          'unit' => 'px',
          'size' => 18,
        ],
        'size_units' => [ 'px', 'em' ],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
            'step' => 1,
          ],
          'em' => [
            'min' => 0,
            'max' => 100,
            'step' => 1,
          ]
        ],
        'selectors' => [
          '{{WRAPPER}} .post-grid .entry-thumbnail:hover .entry-overlay > i' => 'font-size: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();
  }

  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );

    wp_register_style(
      'hubspot-post-grid',
      PLUGIN_URL . '/assets/css/hubspot-post-grid.css',
      false,
      PLUGIN_VERSION
    );

    wp_register_script(
      'hubspot-post-grid',
      PLUGIN_URL . '/assets/js/hubspot-post-grid.js',
      [
        'elementor-frontend', // dependency
      ],
      PLUGIN_VERSION,
      true // in_footer
    );
  }

  public function get_script_depends() {
    return [ 'hubspot-post-grid' ];
  }

  public function get_style_depends() {
    return [ 'hubspot-post-grid' ];
  }

}

# Elementor Custom Widget

**Structure**

* elementor-hubspot-bulb.php   // register plugin in WordPress
* plugin.php    // include and register Elementor custom widgets
* widgets/   // Elementor custom widget classes

__Key Methods__

When creating a custom widget, we have some following key functions need to be implemented.
* _get_name()_: widget name
* _get_title()_: widget title
* _get_categories()_: categories your widget should belongs
* __register_controls()_: definition of fields/selection/... your widget should have
* _render()_: generating final HTML
* _get_script_depends()_: register widget's Javascript
* _get_style_depends()_: register widget's CSS

__References__

[Official Elementor Developer Resources](https://developers.elementor.com)

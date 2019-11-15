(function($) {
  /**
   * @param $scope The Widget wrapper element as a jQuery element
   * @param $ The jQuery alias
   */
  var WidgetHandler = function($scope, $) {

  };

  // Make sure you run this code under Elementor.
  $(window).on('elementor/frontend/init', () => {
    elementorFrontend.hooks.addAction('frontend/element_ready/hubspot-post-grid.default', $element => {
      elementorFrontend.elementsHandler.addHandler(WidgetHandler, {
        $element,
      });
    });
  });
})(jQuery);

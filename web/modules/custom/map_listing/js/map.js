/**
 * @file
 * Contains the definition of the behaviour customMapDataTest.
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  /**
   * Attaches the JS behavior.
   */
  Drupal.behaviors.customMapDataTest = {
    attach: function (context, settings) {
      if ($("#map-container").length > 0) {
          const map = new mapgl.Map('map-container', {
            center: [55.51907632760265, 25.407133194140126],
            zoom: 12,
            key: 'c996adcc-ccd5-11ea-92be-2b9d05d6d4d1',
            style: 'c080bb6a-8134-4993-93a1-5b4d8c36a59b'
          });
          var coords = drupalSettings.map_points;
          console.log(coords);
          coords.forEach((coord) => {
              const marker = new mapgl.Marker(map, {
                  coordinates: coord,
              });
          });
      }
    }
  };
})(jQuery, Drupal, drupalSettings);

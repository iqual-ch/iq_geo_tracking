(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.iqGeoTracking = {
      attach: function attach(context, settings) {
        if (drupalSettings.iq_geo_tracking && drupalSettings.iq_geo_tracking.check == true) {

            if (typeof $.cookie('iq_gt') === 'undefined') {
                var checkUrl = "/tracking/check?_format=json";

                if (drupalSettings.iq_geo_tracking.baseUrl) {
                    checkUrl = drupalSettings.iq_geo_tracking.baseUrl + checkUrl;
                }

                $.ajax({
                    url: checkUrl,
                    method: "GET",
                    headers: {
                      "Content-Type": "application/json"
                    },
                    success: function(data, status, xhr) {
                      if (data && data[0] == true) {
                        window.dataLayer = window.dataLayer || [];
                        window.dataLayer.push({"event": "iq_gt_allowed"});
                      }
                    }
                  });
            }
        }
      }
    };
})(jQuery, Drupal, drupalSettings);
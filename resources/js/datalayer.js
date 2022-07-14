(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.GeoTrackingLimit = {
      attach: function attach(context, settings) {
        if (drupalSettings.gtl && drupalSettings.gtl.check == true) {

            if (typeof $.cookie('gtl') === 'undefined') {
                var checkUrl = "/tracking/check?_format=json";

                if (drupalSettings.gtl.baseUrl) {
                    checkUrl = drupalSettings.gtl.baseUrl + checkUrl;
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
                        window.dataLayer.push({"event": "gtl_allowed"});

                        if (typeof $.cookie('gtl') === 'undefined') {
                          $.cookie('gtl', '1', {path: '/'});
                        }

                        window.dispatchEvent(new Event('gtl_allowed'));
                      } else if (data && data[0] == false) {
                        if (typeof $.cookie('gtl') === 'undefined') {
                          $.cookie('gtl', '-1', {path: '/'});
                        }
                        
                        window.dispatchEvent(new Event('gtl_disallowed'));
                      } else {}
                    }
                  });
            }
        }
      }
    };
})(jQuery, Drupal, drupalSettings);
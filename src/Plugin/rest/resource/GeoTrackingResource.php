<?php
namespace Drupal\geo_tracking_limit\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

/**
 * Endpoint to check geo location of user for tracking.
 *
 * @RestResource(
 *   id = "geo_tracking_resource_get",
 *   label = @Translation("Geo Tracking Resource"),
 *   uri_paths = {
 *      "canonical" = "/tracking/check"
 *   }
 * )
 */
class GeoTrackingResource extends ResourceBase {

  /**
   * Sets a cookie and returns true if tracking is allowed
   * in the geographic region of the visitor (CH)
   * 
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
    \Drupal::service('page_cache_kill_switch')->trigger();
    $cookies = \Drupal::request()->cookies;
    
    // Only check geolocation if a cookie is not present
    if ($cookies->has('gtl')) {
      if ($cookies->get('gtl') > 0) {
        $response = [TRUE];
      } else {
        $response = [FALSE];
      }
    } else {
      // Geolocate visitor

      /** @var \Drupal\smart_ip\SmartIpLocation $location */
      $location = \Drupal::service('smart_ip.smart_ip_location');

      // \Drupal::logger('geo_tracking_limit')->notice('Smart IP Country: ' . $location->get('countryCode'));

      if ($location->get('countryCode') == 'CH') {
        // Set a cookie for trackable visitors
        $this->setGeoTrackingCookie('1');
        
        $response = [TRUE];
      } else {
        // Set a cookie for non-trackable visitors
        $this->setGeoTrackingCookie('-1');

        $response = [FALSE];
      }

      // Remove smart_ip data from user
      $location->delete();
  
      // Remove smart_ip data from Drupal session
      // Prevents creation of session for anonymous visitors
      $request = \Drupal::request();
      $session = $request->getSession();
      $session->remove('smart_ip');
    }

    return new ResourceResponse($response);
  }

  /**
   * Sets the geo tracking cookie
   */
  protected function setGeoTrackingCookie($value) {
    setcookie('gtl', $value, [
      'expires' => 0,
      'path' => '/',
      'domain' => "." . str_replace("www.", "", \Drupal::request()->getHost()),
      'secure' => true,
      'httponly' => false,
      'samesite' => 'None'
      ]);
  }
}
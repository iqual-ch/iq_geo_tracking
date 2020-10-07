<?php
namespace Drupal\iq_geo_tracking\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

/**
 * Endpoint to check geo location of user for tracking.
 *
 * @RestResource(
 *   id = "geo_tracking_resource_get",
 *   label = @Translation("GEO Tracking Resource"),
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
    if ($cookies->has('iq_gt')) {
      if ($cookies->get('iq_gt') > 0) {
        $response = [TRUE];
      } else {
        $response = [FALSE];
      }
    } else {
      // Geolocate visitor

      // Block Smart IP from storing a current user's location
      \Drupal\smart_ip\SmartIp::setSession('smart_ip_user_share_location_permitted', FALSE);

      /** @var \Drupal\smart_ip\SmartIpLocation $location */
      $location = \Drupal::service('smart_ip.smart_ip_location');

      // \Drupal::logger('iq_geo_tracking')->notice('Smart IP Country: ' . $location->get('countryCode'));

      if ($location->get('countryCode') == 'CH') {
        // Set a cookie for trackable visitors
        $this->setGeoTrackingCookie('1');
        
        $response = [TRUE];
      } else {
        // Set a cookie for non-trackable visitors
        $this->setGeoTrackingCookie('-1');

        $response = [FALSE];
      }
    }

    return new ResourceResponse($response);
  }

  /**
   * Sets the geo tracking cookie
   */
  protected function setGeoTrackingCookie($value) {
    setcookie('iq_gt', $value, [
      'expires' => 0,
      'path' => '/',
      'domain' => "." . str_replace("www.", "", \Drupal::request()->getHost()),
      'secure' => true,
      'httponly' => false,
      'samesite' => 'None'
      ]);
  }
}
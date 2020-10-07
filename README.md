# iqual Geo Tracking Module

This module adds the capability to only track visitors from a certain geographic location (Switzerland) with the Google Tag Manager. It sets a cookie (`iq_gt`) and fires a dataLayer event (`iq_gt_allowed`) when a trackable user is first identified.

## Dependecies

* `smart_ip` (for IP geo lookup)
* `google_tag` (or similiar for GTM integration)

## Installation

1. Setup the [smart_ip module](https://www.drupal.org/project/smart_ip)
   * Install an IP datasource (e.g. MaxMind GeoIP2 Lite Country database)
   * Roles to Geolocate: anonymous and authenticated users
   * Acquire/update user's geolocation on specific Drupal native pages: `/tracking/check`
2. Install this module
3. Enable the `/tracking/check:GET` REST endpoint and allow access for anonymous visitors as well as authenticated users (in `/admin/config/services/rest`).
4. Setup the Google Tag Manager container to support the new functionality

## Google Tag Manager Setup

1. Create a first-party cookie variable for the cookie name `iq_gt`
2. Create a custom event trigger for the event name `iq_gt_allowed`
3. Add the first-party cookie to existing triggers as a condition so that the trigger is only fired if the cookie value matches `1`. Apply this to all triggers that are only allowed to fire for trackable visitors. (e.g. PDF link clicks)
3. Add the custom event trigger to existing tags that only fire once per page visit. (e.g. Google Analytics pageview)

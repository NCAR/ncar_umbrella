<?php

namespace Drupal\ucar_alert;


class UcarAlertApiService {

  /** @var string The cache id */
  protected $cid = 'ucar-alert';

  /**
   * Get the alert json from api or cache
   * @return bool|Object
   */
  public function getAlert() {

    $alert = $this->getAlertFromCache();

    if(!$alert) {
      $alert = $this->getAlertFromApi();

      if(isset($alert->field_alert_body)) {
        $this->setAlertInCache($alert);
      }
    }

    return $alert;
  }

  /**
   * Get the alert object from News site API, or false if no alert
   * @return bool|object
   */
  protected function getAlertFromApi() {
    $alert_api_url = \Drupal::config('ucar_alert.settings')->get('url');
    if(!$alert_api_url) return false;

    $response = \Drupal::httpClient()->get($alert_api_url);
    if($response->getStatusCode() == '200') {
      $alert = json_decode($response->getBody());
    }
    return isset($alert[0]) ? $alert[0] : false;

  }

  /**
   * Get the alert object from Drupal cache, or false if not in cache
   * @return bool|object
   */
  protected function getAlertFromCache() {

    $alert = false;

    if($cache = \Drupal::cache()->get($this->cid)) {
      $alert = $cache->data;
    }

    return $alert;
  }

  /**
   * Save alert object in cache for 10 minutes
   * @param $alert
   */
  protected function setAlertInCache($alert) {
    \Drupal::cache()->set($this->cid, $alert, time() + 600);
  }
}
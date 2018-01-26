<?php

namespace Drupal\ucar_alert\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 *
 * @Block(
 *   id = "ucar_alert_block",
 *   admin_label = @Translation("UCAR Alert block"),
 * )
 */

class UcarAlertBlock extends BlockBase {

  /** @var string Full cookie name in Drupal will be "Drupal.visitor.$cookie_name" */
  protected $cookie_name = 'ucar-alert';

  /**
   * {@inheritdoc}
   */
  public function build() {

    /** @var \Drupal\ucar_alert\UcarAlertApiService $alert_api */
    $alert_api = \Drupal::service('ucar_alert_api.manager');

    $alert = $alert_api->getAlert();
    if(!$alert) return [];

    $cookie = $this->getAlertCookie();
    if($cookie) {
      //alert updated? delete cookie
      if($cookie != $alert->changed) {
        user_cookie_delete($this->cookie_name);
      }
      else {//don't show block, user closed it, cookie was set and alert not updated since
        return [];
      }
    }

    $alert->icon = $this->getAlertIcon($alert->field_alert_type);

    return [
      '#theme' => 'ucar_alert_block',
      '#alert' => $alert,
    ];
  }

  /**
   * Never cache the rendered template, because it's just easier that way
   * @return int
   */
  public function getCacheMaxAge() {
    return 0;
  }

  /**
   * Get the alert cookie value (the changed date of the alert), or false if no cookie
   * @return string|false
   */
  protected function getAlertCookie() {
    //Can't seem to access cookies from "outside" of Drupal, i.e. without "Drupal.$cookie" name
    //Also, dots (.) and spaces ( ) in cookie names are replaced with underscores (_). What?
    //http://php.net/manual/en/reserved.variables.cookies.php
    $cookie_key = 'Drupal_visitor_' . $this->cookie_name;
    return isset($_COOKIE[$cookie_key]) ? $_COOKIE[$cookie_key] : false;
  }

  /**
   * Get the fa-icon name based on the type of alert (primary, warning, alert)
   *
   * @param $alert_type
   *
   * @return string
   */
  protected function getAlertIcon($alert_type) {
    $alert_icons = [
      'primary' => 'info-circle',
      'warning' => 'exclamation-triangle',
      'alert' => 'exclamation-circle',
    ];

    return isset($alert_icons[$alert_type]) ? $alert_icons[$alert_type] : '';
  }
}

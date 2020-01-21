<?php

namespace Drupal\user_login_admin\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubscriber.
 *
 * @package Drupal\user_login_admin\Routing
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $admin_routes = ['user.login', 'user.login.http', 'user.pass'];
    foreach ($collection->all() as $name => $route) {
      if (in_array($name, $admin_routes)) {
        $route->setOption('_admin_route', TRUE);
      }
    }
  }

}

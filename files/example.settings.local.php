<?php

/**
 * Assertions.
 *
 * The Drupal project primarily uses runtime assertions to enforce the
 * expectations of the API by failing when incorrect calls are made by code
 * under development.
 *
 * @see http://php.net/assert
 * @see https://www.drupal.org/node/2492225
 *
 * If you are using PHP 7.0 it is strongly recommended that you set
 * zend.assertions=1 in the PHP.ini file (It cannot be changed from .htaccess
 * or runtime) on development machines and to 0 in production.
 *
 * @see https://wiki.php.net/rfc/expectations
 */
assert_options(ASSERT_ACTIVE, TRUE);
\Drupal\Component\Assertion\Handle::register();

/**
 * Enable local development services.
 */
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';

/**
 * Show all error messages, with backtrace information.
 *
 * In case the error level could not be fetched from the database, as for
 * example the database connection failed, we rely only on this value.
 */
#$config['system.logging']['error_level'] = 'verbose';

/**
 * Disable CSS and JS aggregation.
 */
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;

/**
 * Disable the render cache (this includes the page cache).
 *
 * Note: you should test with the render cache enabled, to ensure the correct
 * cacheability metadata is present. However, in the early stages of
 * development, you may want to disable it.
 *
 * This setting disables the render cache by using the Null cache back-end
 * defined by the development.services.yml file above.
 *
 * Do not use this setting until after the site is installed.
 */
$settings['cache']['bins']['render'] = 'cache.backend.null';

/**
 * Disable Internal Page Cache.
 *
 * Note: you should test with Internal Page Cache enabled, to ensure the correct
 * cacheability metadata is present. However, in the early stages of
 * development, you may want to disable it.
 *
 * This setting disables the page cache by using the Null cache back-end
 * defined by the development.services.yml file above.
 *
 * Only use this setting once the site has been installed.
 */
$settings['cache']['bins']['page'] = 'cache.backend.null';

/**
 * Disable Dynamic Page Cache.
 *
 * Note: you should test with Dynamic Page Cache enabled, to ensure the correct
 * cacheability metadata is present (and hence the expected behavior). However,
 * in the early stages of development, you may want to disable it.
 */
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';

/**
 * Allow test modules and themes to be installed.
 *
 * Drupal ignores test modules and themes by default for performance reasons.
 * During development it can be useful to install test extensions for debugging
 * purposes.
 */
$settings['extension_discovery_scan_tests'] = TRUE;

/**
 * Enable access to rebuild.php.
 *
 * This setting can be enabled to allow Drupal's php and database cached
 * storage to be cleared via the rebuild.php page. Access to this page can also
 * be gained by generating a query string from rebuild_token_calculator.sh and
 * using these parameters in a request to rebuild.php.
 */
$settings['rebuild_access'] = TRUE;

/**
 * Skip file system permissions hardening.
 *
 * The system module will periodically check the permissions of your site's
 * site directory to ensure that it is not writable by the website user. For
 * sites that are managed with a version control system, this can cause problems
 * when files in that directory such as settings.php are updated, because the
 * user pulling in the changes won't have permissions to modify files in the
 * directory.
 */
$settings['skip_permissions_hardening'] = TRUE;

/**
 * Uncompressed JS.
 *
 * You can set the site to use the uncompressed rather than the compressed
 * javascript assets. This is controlled by a conditional statement in the
 * html template.
 */
$config['system.site']['uncompressedjs'] = FALSE;

/**
 * Configuration Split.
 *
 * Define which configuration split profile to use dependant on the environment.
 */
$config['config_split.config_split.dev']['status'] = TRUE;
$config['config_split.config_split.prod']['status'] = FALSE;

/**
 * Error reporting level.
 *
 * Change the logging to enable all logging to display. This is advised as
 * errors and notices should be spotted and fixed during development.
 */
$config['system.logging']['error_level'] = 'verbose';

/**
 * Trusted host configuration.
 *
 * Drupal core can use the Symfony trusted host mechanism to prevent HTTP Host
 * header spoofing.
 *
 * To enable the trusted host mechanism, you enable your allowable hosts
 * in $settings['trusted_host_patterns']. This should be an array of regular
 * expression patterns, without delimiters, representing the hosts you would
 * like to allow.
 *
 * For example:
 * @code
 * $settings['trusted_host_patterns'] = array(
 *   '^www\.example\.com$',
 * );
 * @endcode
 * will allow the site to only run from www.example.com.
 *
 * If you are running multisite, or if you are running your site from
 * different domain names (eg, you don't redirect http://www.example.com to
 * http://example.com), you should specify all of the host patterns that are
 * allowed by your site.
 *
 * For example:
 * @code
 * $settings['trusted_host_patterns'] = array(
 *   '^example\.com$',
 *   '^.+\.example\.com$',
 *   '^example\.org$',
 *   '^.+\.example\.org$',
 * );
 * @endcode
 * will allow the site to run off of all variants of example.com and
 * example.org, with all subdomains included.
 */
# $settings['trusted_host_patterns'] = [];

/**
 * Override Minify module HTML minification.
 *
 * Minifying HTML will remove twig debugging comments, so this turns it off for
 * local development. The module should not be installed locally but this is
 * here just in case.
 */

$config['system.performance']['minifyhtml']['minify_html'] = 0;

/**
 * Search API Solr Settings.
 *
 * Where sites share the same settings but each use a different core
 * we need to define this core locally.
 *
 * This is currently configured for the default Docker configuration and
 * may need changing per environment.
 */
$config['search_api.server.solr_server']['backend_config']['connector_config']['core'] = 'solr';
$config['search_api.server.solr_server']['backend_config']['connector_config']['port'] = '8983';
$config['search_api.server.solr_server']['backend_config']['connector_config']['path'] = '/solr';
$config['search_api.server.solr_server']['backend_config']['connector_config']['host'] = 'solr';

/**
 * Redis Configuration.
 */
$conf['chq_redis_cache_enabled'] = FALSE;
if (isset($conf['chq_redis_cache_enabled']) && $conf['chq_redis_cache_enabled']) {
  $settings['redis.connection']['interface'] = 'PhpRedis';
  $settings['cache']['default'] = 'cache.backend.redis';
  // Note that unlike memcached, redis persists cache items to disk so we can
  // actually store cache_class_cache_form in the default cache.
  $conf['cache_class_cache'] = 'Redis_Cache';
}

/**
 * Database settings from Docker env vars.
 */
$databaseName = getenv('DATABASE_NAME');
$databaseUser = getenv('DATABASE_USER');
$databasePassword = getenv('DATABASE_PASSWORD');
$databaseHost = getenv('DATABASE_HOST');

$databases['default']['default'] = [
  'database' => ($databaseName !== FALSE) ? $databaseName : '',
  'username' => ($databaseUser !== FALSE) ? $databaseUser : '',
  'password' => ($databasePassword !== FALSE) ? $databasePassword : '',
  'host' => ($databaseHost !== FALSE) ? $databaseHost : '',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
  'prefix' => '',
];

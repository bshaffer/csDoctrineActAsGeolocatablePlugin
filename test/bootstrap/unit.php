<?php

// handle --symfony_dir argument
if (!isset($_SERVER['SYMFONY']))
{
  foreach ($argv as $arg) 
  {
    $params = explode('=', $arg);
    if (isset($params[1]) && $params[0] == '--symfony_dir') 
    {
      $_SERVER['SYMFONY'] = $params[1];
      break;
    }
  }
}

if (!isset($_SERVER['SYMFONY'])) 
{
  // Default Path
  $_SERVER['SYMFONY'] = dirname(__FILE__).'/../../../../lib/vendor/symfony/lib';
}

if (!file_exists($_SERVER['SYMFONY'])) 
{
  throw new Exception(sprintf("Symfony directory%s not found.  Please set \$_SERVER['SYMFONY'] or provide a --symfony_dir argument", isset($symfonyDir) ? " '$symfonyDir'" : ''));
}

require_once $_SERVER['SYMFONY'].'/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

$configuration = new sfProjectConfiguration(dirname(__FILE__).'/../fixtures/project');
require_once $configuration->getSymfonyLibDir().'/vendor/lime/lime.php';

function csDoctrineActAsGeolocatablePlugin_autoload_again($class)
{
  $autoload = sfSimpleAutoload::getInstance();
  $autoload->reload();
  return $autoload->autoload($class);
}
spl_autoload_register('csDoctrineActAsGeolocatablePlugin_autoload_again');

function csDoctrineActAsGeolocatablePlugin_cleanup()
{
  sfToolkit::clearDirectory(dirname(__FILE__).'/../fixtures/project/cache');
  sfToolkit::clearDirectory(dirname(__FILE__).'/../fixtures/project/log');
}
csDoctrineActAsGeolocatablePlugin_cleanup();
copy(dirname(__FILE__).'/../fixtures/project/data/fresh_test_db.sqlite', dirname(__FILE__).'/../fixtures/project/data/test.sqlite');
register_shutdown_function('csDoctrineActAsGeolocatablePlugin_cleanup');

require_once dirname(__FILE__).'/../fixtures/project/config/ProjectConfiguration.class.php';
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', isset($debug) ? $debug : true);
$context = sfContext::createInstance($configuration);
new sfDatabaseManager($configuration);

// so that all notices will appear
error_reporting(E_ALL);

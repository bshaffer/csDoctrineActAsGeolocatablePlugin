<?php


/**
 * Geocode an address using Google's Geocoder API
 *
 * @package    Locatable_Extension
 * @subpackage geocoder
 * @author     Matt Farmer <work@mattfarmer.net>
 * @link       http://code.google.com/apis/maps/documentation/geocoding/index.html
 */
class GoogleGeoCoder
{

  protected
    $geo_url = "http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=",
    $logger = null,
    $mapping = array(
      'postal_code' => 'zipcode',
      'locality' => 'city',
      'neighborhood' => 'city',
      'sublocality' => 'city',
      'administrative_area_level_2' => 'county',
      'administrative_area_level_1' => 'state',
      'country' => 'country',
    ),
    $save_short = array(
      'country',
      'state',
    ),
    $data = array();

  public function __construct( $geocode )
  {
    $this->geocode = $geocode;
    $this->__fetchData();
  }

  public function __call($func, $args)
  {
    //-- Add Generic get* metonds
    switch( substr($func, 0, 3) )
    {
      case 'get':
        $field = sfInflector::underscore(substr($func, 3));
        if ( 0 === strlen($field) )
        {
          return $this->data[$args[0]];
        }
        if ( array_key_exists( $field, $this->data ) )
        {
          return $this->data[$field];
        }
        else
        {
          return null;
        }
        break;
      default:
        throw new Exception('Fatal Error: Call to undefined method '. __CLASS__ .'::'. $func .'() in '. __FILE__ .'  on '. __LINE__);
        break;
    }
  }

  protected function __fetchData()
  {
    $url = $this->geo_url . urlencode($this->geocode);

    $session = curl_init($url);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
    $res = json_decode(curl_exec($session));
    if ( !$res )
    {
      //-- TODO: Pull out the curl response error code here.
      $this->log('err', 'Error looking up location ('.$this->geocode.').  No response given.');
    }
    else
    {
      if ( "OK" != $res->status )
      {
        $this->log('err', 'Error looking up location ('.$this->geocode.').  Status: '.$res->status);
      }
      else
      {
        $this->__parseData( $res->results[0] );
      }
    }
  }

  protected function __parseData($result)
  {
    foreach( $result->address_components as $comp )
    {
      foreach ( $this->mapping as $googleKey => $pluginKey )
      {
        if ( in_array($googleKey, $comp->types) )
        {
          $this->data[$pluginKey] = $comp->long_name;
          if ( in_array($pluginKey, $this->save_short) )
          {
            $this->data[$pluginKey.'_short'] = $comp->short_name;
          }
        }
      }
    }
    $this->data['latitude'] = $result->geometry->location->lat;
    $this->data['longitude'] = $result->geometry->location->lng;
  }

  protected function getLogger()
  {
    if ( is_null($this->logger) )
    {
      $this->logger = sfContext::getInstance()->getLogger();
    }
    return $this->logger;
  }

  protected function log($levelFunc, $msg)
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->getLogger()->$levelFunc( 'GoogleGeoCoder: '.$msg );
    }
  }

}

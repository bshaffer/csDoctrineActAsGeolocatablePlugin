<?php

/**
 * Easily refresh geo cordinates based on a specified set of fields
 *
 * @package    Locatable_Extension
 * @subpackage template
 * @author     Brent Shaffer
 * @author     Matt Farmer <work@mattfarmer.net>
 * @copyright  Copyright (c) 2008 Centre{source}, Brent Shaffer 2008-12-22. All rights reserved.
 */
class Doctrine_Template_Geolocatable extends Doctrine_Template
{    
  /**
   * Array of locatable options
   */  
  protected $_options = array(
    'columns'     => array(
      'latitude'    =>  array(
        'name'    => 'latitude',
        'type'    => 'double',
        'alias'   =>  null,
        'length'  =>  16,
        'options' =>  array('scale' => 10)),
      'longitude'   =>  array(
        'name'    => 'longitude',
        'type'    => 'double',
        'alias'   =>  null,
        'length'  =>  16,
        'options' =>  array('scale' => 10)),
    ),
    'fields'       => array(),
    'distance_unit' => 'miles',
    'geocoder_class' => 'GoogleGeoCoder',
  );

  
  /**
   * Constructor for Locatable Template
   *
   * @param array $options
   *
   * @return void
   */
  public function __construct(array $options = array())
  {
    $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
    
    if (!$this->_options['fields']) 
    {
      throw new sfException('The Geolocatable Behavior requires the "fields" option to be set in your schema');
    }
  }

  /**
   * Set table definition for locatable behavior
   *
   * @return void
   */
  public function setTableDefinition()
  {
    foreach ($this->_options['columns'] as $key => $options)
    {
      $name = $options['name'];

      if (isset($options['alias']))
      {
        $name .= ' as ' . $options['alias'];
      }

      if (!isset($options['options']))
      {
        $options['options'] = array();
      }

      $this->hasColumn($name, $options['type'], $options['length'], $options['options']);
    }
    
    $this->addListener(new Doctrine_Template_Listener_Geolocatable($this->_options));
  }


  public function refreshGeocodes()
  {
    $obj = $this->getInvoker();

    $query = array();
    foreach ($this->_options['fields'] as $field)
    {
      $query[] = $obj->$field;
    }

    $geocoder_class = $this->_options['geocoder_class'];
    $geocoder = new $geocoder_class(implode(', ', $query));

    foreach($this->_options['columns'] as $key => $options )
    {
      $func = 'get'.sfInflector::camelize($options['name']);
      $obj[$options['name']]  = $geocoder->$func();
    }
  }

  public function addDistanceQueryTableProxy($query, $latitude, $longitude, $distance = null)
  {
    $distanceUnit   = $this->_options['distance_unit'];
    $latField       = $this->_options['columns']['latitude']['name'];
    $lngField       = $this->_options['columns']['longitude']['name'];
    $a              = $query->getRootAlias();
    $factor         = $this->_options['distance_unit'] == 'miles' ? '1.1515' : '1.1515 * 1.609344';

    $select = sprintf(
      '((ACOS(SIN(%s * PI() / 180) * SIN(%s.%s * PI() / 180) 
      + COS(%s * PI() / 180) * COS(%s.%s * PI() / 180) * COS((%s - %s.%s) * PI() / 180)) * 180 / PI()) * 60 * %s) 
      AS %s',
      $latitude, $a, $latField, $latitude, $a, $latField, $longitude, $a, $lngField, $factor, $distanceUnit);
  
    $query->addSelect($select);

    if($distance)
    {
      $query->addHaving($distanceUnit.' < ? ', $distance );
    }

    return $query;
  }
}

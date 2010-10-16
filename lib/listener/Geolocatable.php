<?php

/**
 * Easily refresh geo cordinates based on a specified set of fields
 *
 * @package    Locatable_Extension
 * @subpackage listener
 * @author     Brent Shaffer
 * @copyright  Copyright (c) 2008 Centre{source}, Brent Shaffer 2008-12-22. All rights reserved.
 */
class Doctrine_Template_Listener_Geolocatable extends Doctrine_Record_Listener
{
  /**
   * Array of locatable options
   *
   * @var array
   */  
  protected $_options = array();


  /**
   * Constructor for Locatable Template
   *
   * @param array $options
   *
   * @return void
   */  
  public function __construct(array $options)
  {
    $this->_options = $options;
  }


  /**
   * Set the geocodes automatically when a new locatable object is created
   *
   * @param Doctrine_Event $event
   *
   * @return void
   */
  public function preInsert(Doctrine_Event $event)
  {
    $event
      ->getInvoker()
      ->refreshGeocodes();
  }
  
  /**
   * Set the geocodes automatically when a locatable object's locatable fields are modified
   *
   * @param Doctrine_Event $event
   *
   * @return void
   */
  public function preSave(Doctrine_Event $event)
  {
    $object = $event->getInvoker();

    $modified = array_keys($object->getModified());
    if (array_intersect($this->_options['fields'], $modified))
    {
      $object->refreshGeocodes();
    }
  }
}

csDoctrineActAsGeolocatablePlugin
=================================

This plugin is for Doctrine only.  It provides API integration with Google Maps to allow for the automatic fetching
of latitude and longitude for a given model.  This is configurable for any field/fields on your model.  

How It Works
------------

Add the behavior to your model:

    MyModel:
      actAs: [Geolocatable]
      
Geolocatable requires the "fields" argument to work.  This takes an array of fields use to determine
the latitude/longitude.  Provide as many or as few fields as you want, but you must provide at least one:

    MyModel:
      actAs: 
        Geolocatable
          fields: [city, state]
            
Methods
-------

The Geolocatable plugin comes with some methods for geolocation:

####Object Methods:
* refreshGeocodes( $url = null )
    refreshes the geocodes for the object via the Google Maps API.

####Table Methods
* addDistanceQuery( $query, $latitude, $longitude, $distance = null)
    adds distance query to a preexisting query.  
    field "distance" on each object represents the distance away from the passed latitude and longitude.  
    If $distance is not null, results are limited to that distance from the given geocodes.

Here is an example of how to find contacts within 50 miles of Nashville.

    # schema.yml
    Contact:
      actAs: 
        Geolocatable
          fields: [city, state]
    
    # actions.class.php
    $nashville  = array('latitude' => 36.0775432, 'longitude' => -86.7315785);
    $query = Doctrine_Core::getTable('Contact')->createQuery();
    $this->contacts = Doctrine_Core::getTable('Contact')->addDistanceQuery($query, $nashville['latitude'], $nashville['longitude'], 50)->execute();

Todo
----

* Abstract Geolocation into a service passed to the object via Dependency Injection (or created at runtime if it does not exist)

Please contact bshaffer@centresource.com for any comments or questions
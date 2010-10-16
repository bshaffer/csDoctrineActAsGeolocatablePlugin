<?php

/**
 * Adds a container based on a microformat
 *
 * see: http://microformats.org/wiki/geo
 *
 * @param integer $id        div container id
 * @param float   $latitude  geo point latitude
 * @param float   $longitude geo point longitude
 *
 * @return string html content
 */
function geo_gmap_card($id, $latitude, $longitude)
{
  $googleMapService = sfConfig::get('app_service_google_map');
  $googleMapKey     = $googleMapService['key'];

  use_javascript('http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=' . $googleMapKey);
  use_javascript('/csDoctrineActAsGeolocatablePlugin/js/jquery.gmap.js');

  return <<<HTML
<div id="gmap_user_{$id}" class="gmap geo" style="height: 200px">
  <span class="latitude">{$latitude}</span>
  <span class="longitude">{$longitude}</span>
</div>
HTML;
}

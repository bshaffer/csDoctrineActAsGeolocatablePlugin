(function($, jQuery) {
  $(document).ready(function(){
    $('div.gmap').each(function () {
      var lat = $('.latitude', this);
      var lng = $('.longitude', this);

      // display map this is the latest point ;(
      $(this).show();

      // initialize map
      var map = new GMap2(this);
      var point = new GLatLng(lat.html(), lng.html());

      // set center
      map.setCenter(point, 10);

      // add zoom control
      var mapControl = new GSmallZoomControl();
      map.addControl(mapControl);

      // add the default location
      var marker = new GMarker(point);
      map.setCenter(point, 15);
      map.addOverlay(marker);
    });
  });

  $(window).unload(function () {
    GUnload();
  });
}) (jQuery, jQuery);
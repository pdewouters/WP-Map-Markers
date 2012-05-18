// Store locator with customisations
// - custom marker
// - custom info window (using Info Bubble)
// - custom info window content (+ store hours)

var globalSettings = jQuery.parseJSON(wpmm_settings);
var ICON = new google.maps.MarkerImage(globalSettings[0].marker_icon, null, null,
    new google.maps.Point(14, 13));

var SHADOW = new google.maps.MarkerImage(globalSettings[0].marker_shadow, null, null,
    new google.maps.Point(14, 13));

google.maps.event.addDomListener(window, 'load', function() {

  var map = new google.maps.Map(document.getElementById('map-canvas'), {
    center: new google.maps.LatLng(parseFloat(globalSettings[0].map_center_lat), parseFloat(globalSettings[0].map_center_lng)),
    zoom: parseInt(globalSettings[0].default_zoom),
    mapTypeId: eval(globalSettings[0].map_type)
  });

  var panelDiv = document.getElementById('panel');

  var data = new WPmmDataSource;

  var view = new storeLocator.View(map, data, {
    geolocation: true,
    features: data.getFeatures()
  });

  view.createMarker = function(store) {
    var markerOptions = {
      position: store.getLocation(),
      icon: ICON,
      shadow: SHADOW,
      title: store.getDetails().title
    };
    return new google.maps.Marker(markerOptions);
  }

  var infoBubble = new InfoBubble;
  view.getInfoWindow = function(store) {
    if (!store) {
      return infoBubble;
    }

    var details = store.getDetails();

    var html = ['<div class="store"><div class="title">', details.title,
      '</div><div class="address">', details.address, '</div>',
      '<div class="hours misc">', details.hours, '</div></div>'].join('');

    infoBubble.setContent(jQuery(html)[0]);
    return infoBubble;
  };

  new storeLocator.Panel(panelDiv, {
    view: view
  });
});

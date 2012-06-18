// Store locator with customisations
// - custom marker
// - custom info window (using Info Bubble)
// - custom info window content (+ store hours)

//console.log(wpmm_panel_vars);
var globalSettings = jQuery.parseJSON(wpmm_panel_vars.wpmm_settings);



//var ICON = new google.maps.MarkerImage(globalSettings.marker_icon, null, null,
//    new google.maps.Point(14, 13));

var SHADOW = new google.maps.MarkerImage(globalSettings.marker_shadow, null, null,
    new google.maps.Point(14, 13));

google.maps.event.addDomListener(window, 'load', function() {

  var map = new google.maps.Map(document.getElementById('map-canvas'), {
    center: new google.maps.LatLng(parseFloat(wpmm_panel_vars.lat), parseFloat(wpmm_panel_vars.lng)),
    zoom: parseInt(globalSettings.default_zoom),
    mapTypeId: eval(globalSettings.map_type)
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
      icon: store.getDetails().icon,
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
      '<div class="permalink misc"><a href="', details.link, '">view details</a></div><div class="thumb">', details.thumb , '</div>'].join('');

    infoBubble.setContent(jQuery(html)[0]);
    return infoBubble;
  };

  new storeLocator.Panel(panelDiv, {
    view: view
  });
});

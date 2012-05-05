google.maps.event.addDomListener(window, 'load', function() {
  var map = new google.maps.Map(document.getElementById('map-canvas'), {
    center: new google.maps.LatLng(48.85661, 2.35222),
    zoom: 4,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  });

  var panelDiv = document.getElementById('panel');

var data = new storeLocator.StaticDataFeed();

  var stores = parseStores(wpmm_stores);
  console.log(stores);
  data.setStores(stores);

new storeLocator.View(map, data);

  var view = new storeLocator.View(map, data, {
    //geolocation: false,
    //features: data.getFeatures()
  });

  new storeLocator.Panel(panelDiv, {
    view: view
  });
});

parseStores = function(json) {

  var stores = [];
  var rows = jQuery.parseJSON(json);


jQuery.each(rows, function(i, row){

    var position = new google.maps.LatLng(parseFloat(row.store_lat), parseFloat(row.store_lng));
    var store = new storeLocator.Store(row.id, position,null ,{
        title: row.store_name,
        address: row.address,
        link: '<a href="' + row.store_permalink + '">more details</a>'
    });
    stores.push(store);
});
return stores;
};
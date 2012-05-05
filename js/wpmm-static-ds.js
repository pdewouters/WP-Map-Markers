/**
 * @extends storeLocator.StaticDataFeed
 * @constructor
 */
function WPmmDataSource() {
  $.extend(this, new storeLocator.StaticDataFeed);

  var that = this;

  $.getJSON(wpmm_stores, function(json) {
    var stores = parseStores(json);
    that.setStores(stores);
});
}

/**
 * @private
 * @param {string} csv
 * @return {!Array.<!storeLocator.Store>}
 */
WPmmDataSource.prototype.parseStores = function(json) {
  console.log(json);
  var stores = [];
  var rows = csv.split('\n');
  var headings = this.parseRow_(rows[0]);

  for (var i = 1, row; row = rows[i]; i++) {
    row = this.toObject_(headings, this.parseRow_(row));
    var features = new storeLocator.FeatureSet;
    features.add(this.FEATURES_.getById('Wheelchair-' + row.Wheelchair));
    features.add(this.FEATURES_.getById('Audio-' + row.Audio));

    var position = new google.maps.LatLng(row.Ycoord, row.Xcoord);

    var shop = this.join_([row.Shp_num_an, row.Shp_centre], ', ');
    var locality = this.join_([row.Locality, row.Postcode], ', ');

    var store = new storeLocator.Store(row.uuid, position, features, {
      title: row.Fcilty_nam,
      address: this.join_([shop, row.Street_add, locality], '<br>'),
      hours: row.Hrs_of_bus
    });
    stores.push(store);
  }
  return stores;
};

/**
 * Joins elements of an array that are non-empty and non-null.
 * @private
 * @param {!Array} arr array of elements to join.
 * @param {string} sep the separator.
 * @return {string}
 */
WPmmDataSource.prototype.join_ = function(arr, sep) {
  var parts = [];
  for (var i = 0, ii = arr.length; i < ii; i++) {
    arr[i] && parts.push(arr[i]);
  }
  return parts.join(sep);
};

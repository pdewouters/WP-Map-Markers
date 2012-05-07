/**
 * @extends storeLocator.StaticDataFeed
 * @constructor
 */

function WPmmDataSource() {
  jQuery.extend(this, new storeLocator.StaticDataFeed);

  var that = this;

    that.setStores(that.parse_(wpmm_stores));
    
}

/**
 * @const
 * @type {!storeLocator.FeatureSet}
 * @private
 */
WPmmDataSource.prototype.FEATURES_ = new storeLocator.FeatureSet(
  new storeLocator.Feature('Wheelchair-YES', 'Wheelchair access'),
  new storeLocator.Feature('Audio-YES', 'Audio')
);

/**
 * @return {!storeLocator.FeatureSet}
 */
WPmmDataSource.prototype.getFeatures = function() {
  return this.FEATURES_;
};

/**
 * @private
 * @param {string} csv
 * @return {!Array.<!storeLocator.Store>}
 */
WPmmDataSource.prototype.parse_ = function(json) {
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

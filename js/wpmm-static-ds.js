/**
 * @extends storeLocator.StaticDataFeed
 * @constructor
 */

function WPmmDataSource() {
  jQuery.extend(this, new storeLocator.StaticDataFeed);

  var that = this;

if(wpmm_features.length > 0)
    that.setFeatures_(wpmm_features);
if('null' != wpmm_stores )
    that.setStores(that.parse_(wpmm_stores));

}

WPmmDataSource.prototype = {
    FEATURES_ : null,        
    setFeatures_ : function( json ) {
        //Set up an empty FEATURES_ FeatureSet
        this.FEATURES_ = new storeLocator.FeatureSet();
        //avoid the use of "this" within the jQuery loop by creating a local var
        var featureSet = this.FEATURES_;
        // convert features JSON to js object
        var rows = jQuery.parseJSON( json );
        // iterate through features collection
        jQuery.each( rows, function( i, row ) {
            featureSet.add(
                new storeLocator.Feature( row.slug + '-YES', row.name ) );
        });
    }
}
/**
 * @const
 * @type {!storeLocator.FeatureSet}
 * @private
 */
    

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
  
  // convert stores JSON to js object
  var rows = jQuery.parseJSON(json);

var allFeatures = this.FEATURES_;
// iterate through stores collection
jQuery.each(rows, function(i, row){

    // define an array to contain store features as FEATURE object
        var featureSet = new storeLocator.FeatureSet;
    
    // iterate through store features
    jQuery.each( row.store_features, function( j, f ) {
        // create new FEATURE object to append to FEATURESET

        featureSet.add( allFeatures.getById( f.slug + '-YES' ) );

    });

    var position = new google.maps.LatLng(parseFloat(row.store_lat), parseFloat(row.store_lng));
    var store = new storeLocator.Store(row.id, position,featureSet ,{
        title: row.store_name,
        address: row.address,
        link: row.store_permalink
    });
    stores.push(store);
});
return stores;
};

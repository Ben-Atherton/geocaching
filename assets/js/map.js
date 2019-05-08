var customIcons = {
  Traditional: {
    icon: '/wp-content/plugins/geocaching/images/traditional-icon.png'
  },
  APE: {
    icon: '/wp-content/plugins/geocaching/images/ape-icon.png'
  },
  Letterbox: {
    icon: '/wp-content/plugins/geocaching/images/letterbox-icon.png'
  },
  MultiCache: {
    icon: '/wp-content/plugins/geocaching/images/multicache-icon.png'
  },
  Event: {
    icon: '/wp-content/plugins/geocaching/images/event-icon.png'
  },
  MegaEvent: {
    icon: '/wp-content/plugins/geocaching/images/megaevent-icon.png'
  },
  GigaEvent: {
    icon: '/wp-content/plugins/geocaching/images/gigaevent-icon.png'
  },
  CacheInTrashOut: {
    icon: '/wp-content/plugins/geocaching/images/cito-icon.png'
  },
  GPSAdventures: {
    icon: '/wp-content/plugins/geocaching/images/gpsadventures-icon.png'
  },
  Virtual: {
    icon: '/wp-content/plugins/geocaching/images/virtual-icon.png'
  },
  Webcam: {
    icon: '/wp-content/plugins/geocaching/images/webcam-icon.png'
  },
  EarthCache: {
    icon: '/wp-content/plugins/geocaching/images/earthcache-icon.png'
  },
  Mystery: {
    icon: '/wp-content/plugins/geocaching/images/mystery-icon.png'
  },
  Wherigo: {
    icon: '/wp-content/plugins/geocaching/images/wherigo-icon.png'
  },
  PlacedTraditional: {
    icon: '/wp-content/plugins/geocaching/images/placed-icon.png'
  }
};

var customIconsSM = {
  Traditional: {
    icon: '/wp-content/plugins/geocaching/images/traditional-icon-sm.png'
  },
  Mystery: {
    icon: '/wp-content/plugins/geocaching/images/mystery-icon-sm.png'
  },
  PlacedTraditional: {
    icon: '/wp-content/plugins/geocaching/images/placed-icon-sm.png'
  }
};

var customIconsXS = {
  Traditional: {
    icon: '/wp-content/plugins/geocaching/images/traditional-icon-xs.png'
  },
  Mystery: {
    icon: '/wp-content/plugins/geocaching/images/mystery-icon-xs.png'
  },
  PlacedTraditional: {
    icon: '/wp-content/plugins/geocaching/images/traditional-icon-xs.png'
  }
};

function initMap() {
  window.mapBounds = new google.maps.LatLngBounds();
  var map = new google.maps.Map(document.getElementById("map"), {
    center: new google.maps.LatLng(50.9158621, -3.6575515),
    zoom: 9,
    mapTypeControlOptions: {
    mapTypeIds: [ 
      google.maps.MapTypeId.ROADMAP,
      'OSM', 
      google.maps.MapTypeId.SATELLITE, 
      google.maps.MapTypeId.HYBRID, 
      google.maps.MapTypeId.TERRAIN
    ],
    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
    },
    mapTypeControl: true,
    streetViewControl: false,
    scaleControl: true
  });
  
  //Define OSM map type pointing at the OpenStreetMap tile server
  map.mapTypes.set("OSM", new google.maps.ImageMapType({
    getTileUrl: function(coord, zoom) {
      // "Wrap" x (logitude) at 180th meridian properly
      // NB: Don't touch coord.x because coord param is by reference, and changing its x property breakes something in Google's lib 
      var tilesPerGlobe = 1 << zoom;
      var x = coord.x % tilesPerGlobe;
      if (x < 0) {
          x = tilesPerGlobe+x;
      }
      // Wrap y (latitude) in a like manner if you want to enable vertical infinite scroll

      return "https://a.tile.openstreetmap.org/" + zoom + "/" + x + "/" + coord.y + ".png";
    },
    tileSize: new google.maps.Size(256, 256),
    name: "OSM",
    maxZoom: 18
  }));
  
  var infoWindow = new google.maps.InfoWindow;
  
  // Change this depending on the name of your PHP file
  downloadUrl("/wp-content/plugins/geocaching/includes/load-geocache-map.php", function(data) {
    var xml = data.responseXML;
    var markers = xml.documentElement.getElementsByTagName("marker");
    var gMarkers = []; //google maps' markers, not XML markers
    for (var i = 0; i < markers.length; i++) {
      var geocacheID = markers[i].getAttribute("geocacheID");
      var geocacheTitle = markers[i].getAttribute("geocacheTitle");
      var type = markers[i].getAttribute("type");
      var found = markers[i].getAttribute("found");
      var placed = markers[i].getAttribute("placed");
      var point = new google.maps.LatLng(
        parseFloat(markers[i].getAttribute("lat")),
        parseFloat(markers[i].getAttribute("lng")));
      if (placed != null) {
        var html = '<b><a href="https://coord.info/' + geocacheID + '" target="_blank">' + geocacheID + '</a></b><br/>' + geocacheTitle + '<br/><b>Placed:</b> ' + placed;
      } else {
        var html = '<b><a href="https://coord.info/' + geocacheID + '" target="_blank">' + geocacheID + '</a></b><br/>' + geocacheTitle + '<br/><b>Found:</b> ' + found;
      }
      var icon = customIcons[type] || {};
      var iconSM = customIconsSM[type] || {};
      var iconXS = customIconsXS[type] || {};
      var marker = new google.maps.Marker({
        map: map,
        position: point,
        icon: iconXS.icon
      });
      gMarkers.push(marker); //save google maps' markers
      //extend the bounds to include each marker's position
      mapBounds.extend(marker.position);
      //now fit the map to the newly inclusive bounds
      map.fitBounds(mapBounds);
      bindInfoWindow(marker, map, infoWindow, html);
    } // end of markers for loop
    
    // zoom change handler
    map.addListener('zoom_changed', function(){
  		for(var i = 0; i < gMarkers.length; i++){
  			gMarkers[i].setMap(null);
  
  			var zoomCurrent = map.getZoom(),
  				type = markers[i].getAttribute('type');
  
  			if(zoomCurrent <= 11)
  				gMarkers[i].icon = customIconsXS[type].icon;
  			else if(zoomCurrent == 12)
  				gMarkers[i].icon = customIconsSM[type].icon;
        else if(zoomCurrent == 13)
          gMarkers[i].icon = customIconsSM[type].icon;
        else if(zoomCurrent >= 14)
  				gMarkers[i].icon = customIcons[type].icon;
  			gMarkers[i].setMap(map);
  		}
    }); // zoom change handler ends here
    
  }); // end of downloadUrl() call
}

function bindInfoWindow(marker, map, infoWindow, html) {
  google.maps.event.addListener(marker, 'click', function() {
    infoWindow.setContent(html);
    infoWindow.open(map, marker);
  });
}

function downloadUrl(url, callback) {
  var request = window.ActiveXObject ?
  new ActiveXObject('Microsoft.XMLHTTP') :
  new XMLHttpRequest;

  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      request.onreadystatechange = doNothing;
      callback(request, request.status);
    }
  };

  request.open('GET', url, true);
  request.send(null);
}

function doNothing() {}
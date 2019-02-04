	var jQ = jQuery.noConflict();  
	var markers= [];
	var testValue= [];    
    var constantzoomLevel = '';
    var constantzoomLevelStatus = 0; 
    var zoomLatitude = [];
    function loadmarkers(co_ordinates, prezoomlevel,addressSearch,price,northeastlat,northeastlng,southwestlat,southwestlng) { 
   	var disableListener = true;
  	var latitudeCount;
 	var defaultlat;
  	var defaultlng;
  	var center;  	
  	var co_ordinates_trimed = co_ordinates; 
  	var locations = [];
  	var coordinates = [];
  	var datapoints = co_ordinates_trimed.split('@|@');
  	var product_id; 
	if(datapoints.length > 1) { 	
		latitudeCount = 'm'; 
       for(k=0;k<(datapoints.length-1);k++) {
           var temp = datapoints[k].split('#@@A');
           var details = '<div class="search-result-container"><a href="' + temp[0] + '" title="' + temp[3] + '"  class="matte2-media-box rslides1" style="text-decoration:none; text-transform:capitalize;"><div class="slider_showface"><img style="width: 100%;" src="' + temp[1] + '" width="220" height="180"><div class="map-price">' + temp[6] + '</div></div><div class="data-details"><h4>' + temp[3] + '</h4><h3>' + temp[2] + '</h3></div></a></div>';
           
           var coordinates = [details, temp[4], temp[5],temp[7]];
           locations.push(coordinates);                
       }
	 product_id = temp[7];
	 defaultlat = temp[4];
	 defaultlng = temp[5];
	} else {       
		disableListener=false;
		latitudeCount = 's'; 
       var coordinatesvalues = jQ('#default_map_location').html();
       var datapointsdefault = coordinatesvalues.split('@|@');
       var details = '';       
	   var coordinates = [details, datapointsdefault[0], datapointsdefault[1],datapointsdefault[2]];
	   locations.push(coordinates);
       defaultlat = datapointsdefault[0];
       defaultlng = datapointsdefault[1];
	}
   var iconURLPrefix = 'http://maps.google.com/mapfiles/ms/icons/';	
   var icons = [
     iconURLPrefix + 'red-dot.png',
     iconURLPrefix + 'green-dot.png',
     iconURLPrefix + 'blue-dot.png',
     iconURLPrefix + 'orange-dot.png',
     iconURLPrefix + 'purple-dot.png',
     iconURLPrefix + 'pink-dot.png',
     iconURLPrefix + 'yellow-dot.png'
   ]
  var icons_length = icons.length;
   var minzoomlevel;
   if(prezoomlevel){
	   minzoomlevel = prezoomlevel;
   }else{
	   minzoomlevel = 1;
   }
  var map = new google.maps.Map(document.getElementById('map'), {
   	  minZoom: minzoomlevel, 
   	  maxZoom: 18, 
      scrollwheel: false,
      center: new google.maps.LatLng(defaultlat, defaultlng),
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      mapTypeControl: false,
      streetViewControl: false,
      panControl: false,
      zoomControlOptions: {
     	 position: google.maps.ControlPosition.TOP_LEFT
  	}
});  
	    disableListener = true;
	    var zoommode = 'off';
	    var mapZoomLevelStatusC='';	   
	    var listener1 = google.maps.event.addListener(map,'zoom_changed', function () {       
				var center = map.getCenter();				
				if(disableListener==false){				
		    	if(zoommode == 'on') { 		
				var zoomLatLong = map.getCenter().toString();				
		  		zoomLatLong = zoomLatLong.replace("(","").replace(")","").replace(", ",",");
		  		var zoomLevel = map.getZoom();		  		
	  		if(jQ('#searchmap:checked').val() == 1) {	
					ajaxSearchResult('',zoomLatLong,zoomLevel);
		  		}
	    	} } 
	    	zoommode = 'on';
	});
	google.maps.event.addListener(map, 'dragend', function() { 
    	if(zoommode == 'on') { 		
			var zoomLatLong = map.getCenter().toString(); 
	  		zoomLatLong = zoomLatLong.replace("(","").replace(")","").replace(", ",",");
	  		var zoomLevel = map.getZoom();	  		
			if(jQ('#searchmap:checked').val() == 1) { 
				ajaxSearchResult('',zoomLatLong,zoomLevel);
			}
	    }
	    zoommode = 'on';
	});	    
	if(latitudeCount != 's') {
	    var infowindow = new google.maps.InfoWindow();
	    var marker, i, j;
	    var markers = new Array();
	    var iconCounter = 0;		    
	    for (i = 0; i < locations.length; i++) { 
	    	var pidForMarker = locations[i][3];
	    	testValue[pidForMarker] = marker = new MarkerWithLabel({
	    	       position: new google.maps.LatLng(locations[i][1], locations[i][2]),	    	      
	    	       map: map,
	    	       labelContent: price[i],
	    	       labelAnchor: new google.maps.Point(22, 0),
	    	       labelClass: "labels", // the CSS class for the label
	    	       labelStyle: {opacity: 0.75},
	    	       id: 'map_pin_'+locations[i][3],
	    	       icon:'images/transparent-1x1.png'
	    	     });	 
	    	        
	      markers.push(marker);	
	      google.maps.event.addListener(map, 'click', function(event) {
	    	  infowindow.close();
	        });	      
      	  google.maps.event.addListener(marker, 'click', (function(marker, i) {
        	return function() {    
          infowindow.setContent(locations[i][0]);
          infowindow.open(map, marker);	          
        }
      })(marker, i));
      google.maps.event.addListener(marker, 'mouseover', (function(marker, i) {
	        return function() {
	         jQ("#product_map_over_"+locations[i][3]).addClass("active");
	      }
	      })(marker, i));	      
      google.maps.event.addListener(marker, 'mouseout', (function(marker, i) {
	        return function() {
	         jQ("#product_map_over_"+locations[i][3]).removeClass( "active" );
	        }
	      })(marker, i));       
     }	   
	    var bounds = new google.maps.LatLngBounds(
	    	    new google.maps.LatLng(southwestlat,southwestlng),
	    	    new google.maps.LatLng(northeastlat, northeastlng)
	    	);
	    jQ.each(markers, function (index, marker) {
	        bounds.extend(marker.position);
	      });
	    	var center = bounds.getCenter(); 
	    	var x = bounds.contains(center);	
	    	map.setCenter(bounds.getCenter());	    	
			map.fitBounds(bounds);
	}else{
		var bounds = new google.maps.LatLngBounds(
				new google.maps.LatLng(southwestlat,southwestlng),
	    	    new google.maps.LatLng(northeastlat,northeastlng)	    	    
	    	);		 
	    	var center = bounds.getCenter();	    	
	    	var x = bounds.contains(center);	    	
	    	map.setCenter(bounds.getCenter());
			map.fitBounds(bounds);
	}
	disableListener=false;
    }   
    function hintMarkerInMap(value) {
    	
    }
    function outHintMarkerInMap(value) {
    }
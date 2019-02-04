function mapLogic (currentLatitude,currentLongitude,address) {	
		   geocoder = new google.maps.Geocoder();
			var latlng = new google.maps.LatLng(currentLatitude,currentLongitude);
			var myOptions = {
		zoom: 15,
		center: latlng,
			mapTypeControl: true,
			mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
			navigationControl: true,
		mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		if (geocoder) {
	geocoder.geocode( { 'latLng': latlng    }, function(results, status) {
	if (status == google.maps.GeocoderStatus.OK) {
  		var lat = results[0].geometry.location.lat();
  		var lng = results[0].geometry.location.lng();
  		codeLatLng(lat, lng);  		
	if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
	map.setCenter(results[0].geometry.location);
          var infowindow = new google.maps.InfoWindow(
    { content: '<b>'+address+'</b>',
      size: new google.maps.Size(150,50)
    });
		var marker = new google.maps.Marker({
    position: results[0].geometry.location,
    map: map,
    draggable:true,
    title:address,
    icon:map_marker_icon
 });
	  	google.maps.event.addListener(marker, 'dragend', function(marker){

   		  var latLng = marker.latLng;	
   		 currentLatitude = latLng.lat();
   		currentLongitude = latLng.lng();
   		jQ("#latitude").val(currentLatitude);
   		jQ("#longitude").val(currentLongitude);
   	 });
 google.maps.event.addListener(marker, 'click', function() {
    infowindow.open(map,marker);
 });
	 } else {
 alert("No results found");
	 }
	 }
	 });
	 }
}
function codeLatLng(lat, lng) {
	var city = '';
	var region = '';
	var country = '';
    var latlng = new google.maps.LatLng(lat, lng);   
    geocoder.geocode({'latLng': latlng}, function(results, status) {    	
	    if (status == google.maps.GeocoderStatus.OK) {
	    	console.log(results[1]);
			if (results[1]) {
			    var indice=0;
			    for (var j=0; j<results.length; j++) {
				    if (results[j].types[0]=='locality') {
					    indice=j;
					    break;
				    }
			    }
			    if(j == results.length ) {
                    j=j-1;
				    
			    }
				for (var i=0; i<results[j].address_components.length; i++) {
				    if (results[j].address_components[i].types[0] == "locality") {
					    //this is the object you are looking for
					    city = results[j].address_components[i];
				    }
				    if (results[j].address_components[i].types[0] == "administrative_area_level_1") {
					    //this is the object you are looking for
					    region = results[j].address_components[i];
				    }
				    if (results[j].address_components[i].types[0] == "country") {
					    //this is the object you are looking for
					    country = results[j].address_components[i];
				    }
			    }				
				if(city.long_name){
					document.getElementById("city").value = city.long_name;
				}else{
					document.getElementById("city").value = '';
				}
				if(region.long_name){
					document.getElementById("state").value = region.long_name;
				}else{
					document.getElementById("state").value = '';
				}
				if(country.long_name){
					document.getElementById("country").value = country.long_name;
				}else{
					document.getElementById("country").value = '';
				}		    
			    document.getElementById("latitude").value = lat;
			    document.getElementById("longitude").value = lng;
			 
		    } else {
		    	alert("Try another address");
		    }
	    } else {
	    }
    });
  	}
/*****************************************************************************
 * Start of File
 *****************************************************************************/
$.fn.createMap = function(argumentForPresetOrCustomQueries){
  map = "";
  var markersArray = [];
  var infoWindow;

  function initialize(myDiv) {
    var myOptions = {
      // centered at Washington DC
      center: new google.maps.LatLng(38.900919,-77.035446),
      zoom: 11,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    map = new google.maps.Map(myDiv, myOptions);

    /* raj - start of code for adding ward boundaries */ 
    var georssLayer1 = new google.maps.KmlLayer("http://cise.ufl.edu/~neeraj/saferdc/kml/pages/normalLanding/dcWard1.kml",{ preserveViewport: true });
    georssLayer1.setMap(map);
    var georssLayer2 = new google.maps.KmlLayer("http://cise.ufl.edu/~neeraj/saferdc/kml/pages/normalLanding/dcWard2.kml",{ preserveViewport: true });
    georssLayer2.setMap(map);
    var georssLayer3 = new google.maps.KmlLayer("http://cise.ufl.edu/~neeraj/saferdc/kml/pages/normalLanding/dcWard3.kml",{ preserveViewport: true });
    georssLayer3.setMap(map);
    var georssLayer4 = new google.maps.KmlLayer("http://cise.ufl.edu/~neeraj/saferdc/kml/pages/normalLanding/dcWard4.kml",{ preserveViewport: true });
    georssLayer4.setMap(map);
    var georssLayer5 = new google.maps.KmlLayer("http://cise.ufl.edu/~neeraj/saferdc/kml/pages/normalLanding/dcWard5.kml",{ preserveViewport: true });
    georssLayer5.setMap(map);
    var georssLayer6 = new google.maps.KmlLayer("http://cise.ufl.edu/~neeraj/saferdc/kml/pages/normalLanding/dcWard6.kml",{ preserveViewport: true });
    georssLayer6.setMap(map);
    var georssLayer7 = new google.maps.KmlLayer("http://cise.ufl.edu/~neeraj/saferdc/kml/pages/normalLanding/dcWard7.kml",{ preserveViewport: true });
    georssLayer7.setMap(map);
    var georssLayer8 = new google.maps.KmlLayer("http://cise.ufl.edu/~neeraj/saferdc/kml/pages/normalLanding/dcWard8.kml",{ preserveViewport: true });
    georssLayer8.setMap(map); 
    /* raj - end of code for adding ward boundaries */

    infoWindow = new google.maps.InfoWindow();
    google.maps.event.addListener(map, 'click', function() {
          infoWindow.close();
      });
	
	/* Rahul - Invoking the right function -> to show the map for a preset query or a custom query */	
	if ( argumentForPresetOrCustomQueries['filters'].length > 0 ) {
      retrieveCustomMapQuery(argumentForPresetOrCustomQueries);
	}
	else {
      retrievePresetMapQuery(argumentForPresetOrCustomQueries['url']);
	}
	
  }

  function deleteOverlays() {
    if (markersArray) {
      for (i in markersArray) {
        markersArray[i].setMap(null);
      }
      markersArray.length = 0;
    }
  }

  var latArray = [];
  var longitArray = [];
  var valueArray = [];

  function addMarkers() {
    //deleteOverlays(); // Rahul - Not necessary since we are doing this before invoking addMarkers()
    function createMarker(map, position, number) {
      var marker = new google.maps.Marker({
        position: position,
        map: map
      });
      //marker.setTitle(''+roles[number]); // Set each marker's title
      markersArray.push(marker);

      google.maps.event.addListener(marker, 'click', function() {
        var infoText = ""+valueArray[number];
        infoWindow.setContent(infoText);
        infoWindow.open(map, marker);
      });
    }

    for ( var i=0; i<= latArray.length; i++ ) {
      var latLng = new google.maps.LatLng(latArray[i],longitArray[i]);
      createMarker(map, latLng, i);
    }
  }

  function retrievePresetMapQuery(url) {
	var xmlhttp;
	if (window.XMLHttpRequest) {
	  // code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp = new XMLHttpRequest();
	}
	else {
	  // code for IE6, IE5
	  xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.onreadystatechange = function() {
	  if (xmlhttp.readyState!=4) {
		//raj - show loading animation
		$("#mapDiv").hide();
		$("#loadingDiv").show();
	  }
	  else {
		//raj - remove loading animation.
		$("#loadingDiv").hide();
		$("#mapDiv").show();
		/* raj - we must resize because when the map was created in initialize() above,
		** #mapDiv's display was set to none. Hence, the map didn't know what size to be.
		** Since we have now reset display to block, #mapDiv has a size. Triggering a resize
		** will let the Map know. We must also make sure to center the map again after the
		** resize occurs */ 
		var center = map.getCenter();
		google.maps.event.trigger(map, 'resize');
		map.setCenter(center);
		deleteOverlays();
		
		var response = ''+xmlhttp.responseText; //not required if you set content-type to text/plain - neeraj
		var jsonString = response.substring(response.indexOf('{'),response.lastIndexOf('}')+1 ) ; //not required if you set content-type to text/plain - neeraj
		var JSobj = JSON.parse(jsonString); //not required if you set content-type to text/plain - neeraj
		//var JSobj = JSON.parse(xmlhttp.responseText);
		if (JSobj) {
		  var i;
		  var requiredLength = JSobj.LATITUDE.length;
		  latArray.length = requiredLength ; 
		  longitArray.length = requiredLength;
		  valueArray.length = requiredLength;

		  for (i in JSobj.LATITUDE) {
			latArray[i] = parseFloat(JSobj.LATITUDE[i]);
			longitArray[i] = parseFloat(JSobj.LONGITUDE[i]);
			valueArray[i] = JSobj.VALUE[i];
		  }
		  
		  /* Rahul - Now colour specific Wards which have highest or lowest crimes
		  if ( url.search(/ward_with_(highest|lowest)_crimes_/i ) ) {
		    //alert("kml/pages/normalLanding/dcWard"+JSobj.AREA[0]+".kml");
		    //var georssLayer = new google.maps.KmlLayer("kml/pages/normalLanding/dcWard"+JSobj.AREA[0]+".kml");
			//georssLayer.setMap(map);
		  }
		  */
		  addMarkers();
		}
	  }
	}

	xmlhttp.open("POST",url,true); // Rahul - passing the url of the php corresponding to the value selected in the presetMapQueryComboBox
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send();
  }
  
  
  //function retrieveResults() { // Rahul - Changed this function name to retrieveCustomMapQuery() 
  function retrieveCustomMapQuery() {
    var xmlhttp;
    if (window.XMLHttpRequest)
    {
      // code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp=new XMLHttpRequest();
    }
    else
    {
      // code for IE6, IE5
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }

    xmlhttp.onreadystatechange = function() {
      if (xmlhttp.readyState!=4) {
        //raj - show loading animation
        $("#mapDiv").hide();
        $("#loadingDiv").show();
      }
      else{
        //raj - remove loading animation.
        $("#loadingDiv").hide();
        $("#mapDiv").show();
        /* raj - we must resize because when the map was created in initialize() above,
        /* #mapDiv's display was set to none. Hence, the map didn't know what size to be.
        /* Since we have now reset display to block, #mapDiv has a size. Triggering a resize
        /* will let the Map know. We must also make sure to center the map again after the
        /* resize occurs */
        var center = map.getCenter();
        google.maps.event.trigger(map, 'resize');
        map.setCenter(center);
		deleteOverlays();
		
        var response = ''+xmlhttp.responseText; //not required if you set content-type to text/plain - neeraj
        var jsonString = response.substring(response.indexOf('{'),response.lastIndexOf('}')+1 ) ; //not required if you set content-type to text/plain - neeraj
        var JSobj = JSON.parse(jsonString); //not required if you set content-type to text/plain - neeraj
        //var JSobj = JSON.parse(xmlhttp.responseText);
        if (JSobj) {
          var i;
		  if ( JSobj.LATITUDE != null ) {
		    var requiredLength = JSobj.LATITUDE.length;
		    latArray.length = requiredLength ; 
		    longitArray.length = requiredLength;
		    valueArray.length = requiredLength;
		  
		    for (i in JSobj.LATITUDE) {
			  latArray[i] = parseFloat(JSobj.LATITUDE[i]);
			  longitArray[i] = parseFloat(JSobj.LONGITUDE[i]);
			  valueArray[i] = JSobj.VALUE[i];
		    }
		    addMarkers();
		  }
        }
      }
    }

  xmlhttp.open("POST","php/pages/normalLanding/mapCustomQueries.php",true);
  xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  xmlhttp.send(argumentForPresetOrCustomQueries['filters']);
  }
  
  return this.each(function(){
    initialize(this);
  });

}

$(document).ready(function() {
  /***************************************************************************
   * Common
   ***************************************************************************/
  $("#breadCrumb").jBreadCrumb(); //create breadcrumbs
  $("div#mapGraphDiv").tabs(); //add tabbed navigation to right-hand side
  $("#queryDiv").tabs(); //add tabbed navigation to left-hand side
  //$("#mapDiv").createMap(); //create Google Map - Invoking from here just to test
  $("#mapFromDate").datepicker(); //create date picker
  $("#mapToDate").datepicker(); //create date picker
  $("#graphFromDate").datepicker(); //create date picker
  $("#graphToDate").datepicker(); //create date picker
  $("#tabularFromDate").datepicker(); //create date picker
  $("#tabularToDate").datepicker(); //create date picker
  $("#mapFromDate").val("Click to select a date"); //create date picker
  $("#mapToDate").val("Click to select a date"); //create date picker
  $("#graphFromDate").val("Click to select a date"); //create date picker
  $("#graphToDate").val("Click to select a date"); //create date picker
  $("#tabularFromDate").val("Click to select a date"); //create date picker
  $("#tabularToDate").val("Click to select a date"); //create date picker

  /* Hover effects for buttons
     Source: www.filamentgroup.com/examples/buttonFrameworkCSS */
  $(".fg-button:not(.ui-state-disabled)")
    .hover(
      function(){
        $(this).addClass("ui-state-hover");
      },
      function(){
        $(this).removeClass("ui-state-hover");
      }
  )
  .mousedown(function(){
    $(this).parents('.fg-buttonset-single:first').find(".fg-button.ui-state-active").removeClass("ui-state-active");
    if( $(this).is('.ui-state-active.fg-button-toggleable, .fg-buttonset-multi .ui-state-active') ){ $(this).removeClass("ui-state-active"); }
    else { $(this).addClass("ui-state-active"); }
  })
  .mouseup(function(){
   if(! $(this).is('.fg-button-toggleable, .fg-buttonset-single .fg-button,  .fg-buttonset-multi .fg-button') ){
    $(this).removeClass("ui-state-active");
   }
  });

  /***************************************************************************
   * Maps
   ***************************************************************************/
  $("#presetMapBtn").click( /* User wants to select preset map queries */
    function(){
      $("div#mapButtonHolderDiv").hide(); //hide its own parent
      $("div#presetMapQueryHolderDiv").show(); //show the preset query selectors
      $("div#mapPresetFrontBackBtnDiv").show(); //show the submit and back buttons for preset queries
      return false;
    }
  )

  $("#customMapBtn").click( /* User wants to create custom map queries */
    function(){
      $("div#mapButtonHolderDiv").hide(); //hide its own parent
      $("div#customMapQueryHolderDiv").show(); //show the custom query selectors
      $("div#mapCustomFrontBackBtnDiv").show(); //show the submit and back buttons for custom queries
      return false;
    }
  )

  $("#showPresetMapBtn").click( /* Rahul - User wants to show the map for preset queries */
    function(){
      $("#mapGraphDiv").tabs("option", "selected", 0); //switch to the map output tab; important if it isn't already showing

      var val = $('select#presetMapQueryComboBox option:selected').val();
	  var argumentForPresetOrCustomQueries = new Array();
		argumentForPresetOrCustomQueries['url'] = "";
		argumentForPresetOrCustomQueries['filters'] = "";
      switch(val){
        case '1':
		  argumentForPresetOrCustomQueries['url'] = "php/mapQueries/concentration_first_quarter_1.php" ;
          break;
        case '2':
		  argumentForPresetOrCustomQueries['url'] = "php/mapQueries/concentration_second_quarter_2.php" ;
          break;
        case '3':
		  argumentForPresetOrCustomQueries['url'] = "php/mapQueries/concentration_third_quarter_3.php" ;
          break;
        case '4':
		  argumentForPresetOrCustomQueries['url'] = "php/mapQueries/concentration_fourth_quarter_4.php" ;
          break;
        case '5':
		  argumentForPresetOrCustomQueries['url'] = "php/mapQueries/concentration_of_crimes_5.php";
          break;
        case '6':
		  argumentForPresetOrCustomQueries['url'] = "php/mapQueries/highest_crime_monday_6.php";
          break;
        case '7':
		  argumentForPresetOrCustomQueries['url'] = "php/mapQueries/highest_crime_tuesday_7.php";
          break;
        case '8':
		  argumentForPresetOrCustomQueries['url'] = "php/mapQueries/highest_crime_wednesday_8.php";
          break;
        case '9':
		  argumentForPresetOrCustomQueries['url'] = "php/mapQueries/highest_crime_thursday_9.php";
          break;
        case '10':
		  argumentForPresetOrCustomQueries['url'] = "php/mapQueries/highest_crime_friday_10.php";
          break;
        case '11':
		  argumentForPresetOrCustomQueries['url'] = "php/mapQueries/psa_with_lowest_crimes_11.php";
          break;
        case '12':
		  argumentForPresetOrCustomQueries['url'] = "php/mapQueries/psa_with_highest_crimes_12.php";
          break;
        case '13':
		  argumentForPresetOrCustomQueries['url'] = "php/mapQueries/ward_with_lowest_crimes_13.php";
          break;
        case '14':
		  argumentForPresetOrCustomQueries['url'] = "php/mapQueries/ward_with_highest_crimes_14.php";
          break;
        default:
          alert("Select an option");
      }
	  // Rahul - See which preset query was chosen and pass the corresponding php to createMap()
	  if ( argumentForPresetOrCustomQueries['url'].length > 0 ) {
		$("#mapDiv").createMap(argumentForPresetOrCustomQueries);
	  }
    }
  )

  $("#showCustomMapBtn").click( /* Rahul - User wants to show the map for custom queries */
    function(){
      $("#mapGraphDiv").tabs("option", "selected", 0); //switch to the map output tab; important if it isn't already showing

      var crimeType = $('select#mapCrimeTypeComboBox option:selected').val();
	  var fromDate = $('#mapFromDate').val();
	  if ( fromDate.search(/select a date/i) > 0 )
		$('#mapFromDate').val('01/01/2011');
	  
	  var toDate = $('#mapToDate').val();
	  if ( toDate.search(/select a date/i) > 0 )
		$('#mapToDate').val('12/31/2011');
	  var psaID = $('select#mapPsaIDComboBox option:selected').val();
	  var wardID = $('select#mapWardIDComboBox option:selected').val();
	   
	  //alert("C = "+crimeType+" FDate = "+fromDate+" toDate = "+toDate+" psaID = "+psaID+" Ward ID = "+wardID);
	  var argumentForPresetOrCustomQueries = new Array();
	  argumentForPresetOrCustomQueries['url'] = "";
      argumentForPresetOrCustomQueries['filters'] = "crimeType="+crimeType+
													"&fromDate="+fromDate+
													"&toDate="+toDate+	  
													"&psaID="+psaID+
													"&wardID="+wardID;
      //alert(argumentForPresetOrCustomQueries['filters']);													
	  
	  $("#mapDiv").createMap(argumentForPresetOrCustomQueries);
    }
  )

  $(".mapBackBtn").click( /* User wants to go back to the first map tab page*/
    function(){
      $("div#customMapQueryHolderDiv").hide();
      $("div#presetMapQueryHolderDiv").hide();
      $("div#mapPresetFrontBackBtnDiv").hide(); //hide the submit and back buttons
      $("div#mapCustomFrontBackBtnDiv").hide(); //hide the submit and back buttons
      $("div#mapButtonHolderDiv").show();
      return false;
    }
  )

  /***************************************************************************
   * Graphs
   ***************************************************************************/
  $("#presetGraphBtn").click( /* User wants to select preset graph queries */
    function(){
      $("div#graphButtonHolderDiv").hide(); //hide its own parent
      $("div#presetGraphQueryHolderDiv").show(); //show the preset query selectors
      $("div#graphPresetFrontBackBtnDiv").show(); //show the submit and back buttons for preset queries
      return false;
    }
  )

  $("#showPresetGraphBtn").click( /* User wants to show the graph for preset queries */
    function(){
      $("#mapGraphDiv").tabs("option", "selected", 1); //switch to the graph output tab; important if it isn't already showing
      var val = $('select#presetGraphQueryComboBox option:selected').val();
      var where = "graphDiv";
      var graph;
      switch(val){
        case '1':
          graph = new DCChart(where, "php/queries/total_crimes_month_1.php");
          break;
        case '2':
          graph = new DCChart(where, "php/queries/monthwise_crimes_2.php");
          break;
        case '3':
          graph = new DCChart(where, "php/queries/crime_vs_pop_3.php");
          break;
        case '4':
          graph = new DCChart(where, "php/queries/total_crimes_bar_4.php");
          break;
        case '5':
          graph = new DCChart(where, "php/queries/highest_crime_type_month_5.php");
          break;
        case '6':
          graph = new DCChart(where, "php/queries/lowest_crime_type_month_6.php");
          break;
        case '7':
          graph = new DCChart(where, "php/queries/crime_percentage_per_ward_7.php");
          break;
		case '8':
          graph = new DCChart(where, "php/queries/crime_prone_addresses_8.php");
          break;
		case '9':
          graph = new DCChart(where, "php/queries/crimes_based_on_count_type_address_9.php");
          break;
		case '10':
          graph = new DCChart(where, "php/queries/higest_crime_count_by_type_daywise_10.php");
          break;
		case '11':
          graph = new DCChart(where, "php/queries/highest_crime_psa_lowest_crime_ward_11.php");
          break;
		case '12':
          graph = new DCChart(where, "php/queries/longest_crime_free_duration_12.php");
          break;
		case '13':
          graph = new DCChart(where, "php/queries/lowest_crime_psa_highest_crime_ward_13.php");
          break;
		case '14':
          graph = new DCChart(where, "php/queries/psa_wise_income_crime_14.php");
          break;
		case '15':
          graph = new DCChart(where, "php/queries/psa_wise_poverty_crime_15.php");
          break;
		case '16':
          graph = new DCChart(where, "php/queries/timeline_16.php");
          break;
		case '17':
          graph = new DCChart(where, "php/queries/ward_wise_income_crime_17.php");
          break;
		case '18':
          graph = new DCChart(where, "php/queries/ward_wise_poverty_crime_18.php");
          break;
		case '19':
          graph = new DCChart(where, "php/queries/weekday_wise_crime_19.php");
          break;	  
        default:
          alert("Select an option");
      }
      return false;
    }
  )

  $("#customGraphBtn").click( /* User wants to create custom graph queries */
    function(){
      $("div#graphButtonHolderDiv").hide(); //hide its own parent
      $("div#customGraphQueryHolderDiv").show(); //show the custom query selectors
      $("div#graphCustomFrontBackBtnDiv").show(); //show the submit and back buttons for custom queries
      return false;
    }
  )

  $("#graphXAxisComboBox").change(function(){
    if($("select#graphXAxisComboBox option:selected").val()=="DATE"){ //user wants dates on the X-axis
      $("tr#graphWards").hide();
      $("tr#graphDates").show();
    }
    else if($("select#graphXAxisComboBox option:selected").val()=="WARD"){ //user wants wards on the X-axis
      $("tr#graphDates").hide();
      $("tr#graphWards").show();
    }
  })

  $("#showCustomGraphBtn").click( /* Rahul - User wants to show the graph for custom queries */
    function(){
      $("#mapGraphDiv").tabs("option", "selected", 1); //switch to the graph output tab; important if it isn't already showing
      var yAxisValues = $("#graphYAxisComboBox").val() || [];
      var xAxisValue = $('select#graphXAxisComboBox option:selected').val();
      if ( yAxisValues.length == 0 || xAxisValue.length == 0 ) {
        alert("Select both y-axis and x-axis values");
      }	  
      else {
        var fromDate, toDate, wardID;
        if ( xAxisValue == 'DATE') {
          fromDate = $('#graphFromDate').val();
          if ( fromDate.search(/select a date/i) != -1 ) {
            fromDate = '01/01/2011' ; // Rahul - Explicitly setting fromDate to Jan 1st when the user doesn't choose any date.
            $('#graphFromDate').val('01/01/2011');
          }
          toDate = $('#graphToDate').val();
          if ( toDate.search(/select a date/i) != -1 ) {
            toDate = '12/31/2011' ; // Rahul - Explicitly setting toDate to Dec 31st when the user doesn't choose any date.
            $('#graphToDate').val('12/31/2011');
          }
        }
        else {
          wardID = $("#graphWardIDComboBox").val() || [];
          if ( wardID != 'undefined' ) {
		    if ( wardID.length == 0 ) {
			  alert("Please select a ward");
			  return;
			}			
            wardID = wardID.join();
          }
        }
        
        var where = "graphDiv";
        var graph;
        /* EXAMPLE 
        *  ?yAxisValues=THEFT,ARSON,STOLEN AUTO&xAxisValue=DATE&fromDate=04/01/2011&toDate=10/01/2011&wardID=%
        *  OR
        *  ?yAxisValues=THEFT,ARSON,STOLEN AUTO&xAxisValue=WARD&fromDate=%&toDate=%&wardID=3
        */
        //alert("php/pages/normalLanding/graphCustomQueries.php?yAxisValues="+yAxisValues.join()+"&xAxisValues="+xAxisValue+"&fromDate="+fromDate+"&toDate="+toDate+"&wardID="+wardID);
        //alert("?yAxisValues="+yAxisValues.join()+"&xAxisValue="+xAxisValue+"&fromDate="+fromDate+"&toDate="+toDate+"&wardID="+wardID);
        graph = new DCChart(where, "php/pages/normalLanding/graphCustomQueries.php?yAxisValues="+yAxisValues.join()+"&xAxisValues="+xAxisValue+"&fromDate="+fromDate+"&toDate="+toDate+"&wardID="+wardID);
      }
    }
  )

  $(".graphBackBtn").click( /* User wants to go back to the first graph tab page*/
    function(){
      $("div#customGraphQueryHolderDiv").hide();
      $("div#presetGraphQueryHolderDiv").hide();
      $("div#graphPresetFrontBackBtnDiv").hide(); //hide the submit and back buttons
      $("div#graphCustomFrontBackBtnDiv").hide(); //hide the submit and back buttons
      $("div#graphButtonHolderDiv").show();
      return false;
    }
  )

  /***************************************************************************
   * Tabular
   ***************************************************************************/
  $("#presetTabularBtn").click( /* User wants to select preset tabular queries */
    function(){
      $("div#tabularButtonHolderDiv").hide(); //hide its own parent
      $("div#presetTabularQueryHolderDiv").show(); //show the preset query selectors
      $("div#tabularPresetFrontBackBtnDiv").show(); //show the submit and back buttons for preset queries
      return false;
    }
  )

  /* Rahul - User wants to show the tabular output for preset queries */
  $("#showPresetTabularBtn").click( 
    function(){
      $("#mapGraphDiv").tabs("option", "selected", 2); //switch to the tabular output tab; important if it isn't already showing
      var whichQuery = $('select#presetTabularQueryComboBox option:selected').val();
	  if ( whichQuery == 0 )
	    alert("Please select a query");
      else {
	    //Setup Ajax Object
	    $.ajaxSetup({
		  url:'php/pages/normalLanding/tabularPresetQueries.php',
		  type:'POST'
	    });

	    //Start Ajax task
	    $.ajax({
          data: {'presetQNumber':''+whichQuery},
          beforeSend: function() {
            $('#tabularDiv').hide();
            $('#loadingDiv').show(); 
          },
          success:function(d,s,x){
            $('#loadingDiv').hide();
			$('#tabularDiv').show();
            $("#tabularDiv").html(d);
          }
        });
      }
	   
    }
  )

  $("#customTabularBtn").click( /* User wants to create custom tabular queries */
    function(){
      $("div#tabularButtonHolderDiv").hide(); //hide its own parent
      $("div#tabularCustomDropDownORSQLDiv").show(); //user must either choose drop-down boxes or type in her own SQL
	  $("div#tabularCustomBackBtnDiv").show(); //show the submit and back buttons for preset queries
      return false;
    }
  )

  $("#customTabularBtnDropDown").click( /* User wants to create custom tabular queries by using our dropdown boxes */
    function(){
      $("div#tabularCustomDropDownORSQLDiv").hide();
      $("div#customTabularDropDownDiv").show(); //show the custom query selectors
      $("div#tabularCustomDropDownFrontBackBtnDiv").show(); //show the submit and back buttons for custom queries
	  $("div#tabularCustomBackBtnDiv").hide(); //hide the back button
      return false;
    }
  )

  $("#showCustomDropDownTabularBtn").click( /* User wants to show the tabular output for custom queries formed by selecting from our dropdown boxes */
    function(){
	  $("div#tabularCustomBackBtnDiv").hide(); //hide the back button
      $("#mapGraphDiv").tabs("option", "selected", 2); //switch to the tabular output tab; important if it isn't already showing
      /* Rahul - Code to show tabular output depending on the options chosen in the drop down boxes */
      var crimeType = $('select#tabularCrimeTypeComboBox option:selected').val();
	  if ( crimeType.length == 0 )
	    crimeType = '%' ; 
		
	  var fromDate = $('#tabularFromDate').val();
	  if ( fromDate.search(/select a date/i) != -1 ) {
	    fromDate = '01/01/2011' ;
		$("#tabularFromDate").val('01/01/2011');
      }
      fromDate = ' AND REPORT_DATE >= to_date( \''+fromDate+'\',\'MM/DD/YYYY\')' ;
		
	  var toDate = $('#tabularToDate').val();
	  if ( toDate.search(/select a date/i) != -1 ) {
        toDate = '12/31/2011' ; //' AND REPORT_DATE LIKE \'%\'';
		$('#tabularToDate').val('12/31/2011');
      }
      toDate = ' AND REPORT_DATE <= to_date( \''+toDate+'\',\'MM/DD/YYYY\')' ;
		
	  var psaID = $('select#tabularPsaIDComboBox option:selected').val();
	  if ( psaID.length == 0 )
	    psaID = '%' ;
		
	  var wardID = $('select#tabularWardIDComboBox option:selected').val();
	  if ( wardID.length == 0 )
	    wardID = '%' ;
		
	  /* Rahul - Just to debug 
      var filters = "crimeType="+crimeType+
					"&fromDate="+fromDate+
					"&toDate="+toDate+	  
					"&psaID="+psaID+
					"&wardID="+wardID;
      alert(filters); */
	  	  
      //Show "Loading..." message
      $("#tabularDiv").html("Loading...");
			
      //Get query (Text field value)
      var query = 'SELECT COUNT(*) Number_Of_Crimes FROM CRIME WHERE OFFENCE_TYPE LIKE \''+crimeType+'\' AND PSA_ID LIKE \''+psaID+'\' AND WARD_ID LIKE \''+wardID+'\''+fromDate+toDate;
      //Setup Ajax Object
      $.ajaxSetup({
        url:'php/pages/normalLanding/sqlCustom.php',
        type:'POST'
      });

      //Start Ajax task
      $.ajax({
          data: {'query':''+query},
          beforeSend: function() {
            $('#tabularDiv').hide();
            $('#loadingDiv').show();
          },
          success:function(d,s,x){
              $('#tabularDiv').show();
              $('#loadingDiv').hide();
              $("#tabularDiv").html(d);
          }
      });
    }
	
  )

  $("#customTabularBtnSQL").click( /* User wants to create custom tabular queries by typing in her own SQL */
    function(){
      $("div#tabularCustomDropDownORSQLDiv").hide();
      $("div#customTabularSQLDiv").show(); //show the custom SQL box
      $("textarea#customTabularSQLInput").focus(); //make the SQL input field gain focus
      $("div#tabularCustomSQLFrontBackBtnDiv").show(); //show the submit and back buttons for custom queries
	  $("div#tabularCustomBackBtnDiv").hide(); //hide the back button
      return false;
    }
  )
  
  $("#showCustomSQLTabularBtn").click( /* Rahul - User wants to show the tabular output for custom queries formed by typing in SQL */	
	function(){
	  $("#mapGraphDiv").tabs("option", "selected", 2); //switch to the tabular output tab; important if it isn't already showing
			
      //Get query (Text field value)
      var query = $("#customTabularSQLInput").val();
      
      if ( query.length == 0 ) {
	    alert("Please type in a query");
	  }
	  
	  else {
        //Setup Ajax Object
        $.ajaxSetup({
          url:'php/pages/normalLanding/sqlCustom.php',
          type:'POST'
        });

        //Start Ajax task
        $.ajax({
          data: {'query':''+query},
          beforeSend: function() {
            $('#tabularDiv').hide();
            $('#loadingDiv').show();
          },
          success: function(d,s,x) {
            $('#loadingDiv').hide();
            $('#tabularDiv').show();
            $("#tabularDiv").html(d);
          }
		});
	  }
	});

  $(".tabularBackBtn").click( /* User wants to go back to the first tabular tab page*/
    function(){
      $("div#customTabularDropDownDiv").hide();
      $("div#customTabularSQLDiv").hide();
      $("div#presetTabularQueryHolderDiv").hide();
      $("div#tabularPresetFrontBackBtnDiv").hide(); //hide the submit and back buttons
	  $("div#tabularCustomBackBtnDiv").hide(); //hide the back button
	  $("div#tabularCustomDropDownORSQLDiv").hide();
      $("div#tabularCustomDropDownFrontBackBtnDiv").hide(); //hide the submit and back buttons
      $("div#tabularCustomSQLFrontBackBtnDiv").hide(); //hide the submit and back buttons
      $("div#tabularButtonHolderDiv").show();
      return false;
    }
  )
  
	//Click all buttons with class 'db_schema_show'
	$(".db_schema_show").click(function(){
		newPopupWindow("dbSchemaDisplay.html");	//Call the method for creating a new window with specified url
	});
	
}); // Rahul - END of $document.ready()

	function newPopupWindow(url){	//Method for creating/displaying new window
		newwindow = window.open(url,'dbschemadisplay','height=300,width=500');	//window variable with url, name, and properties
		//if new window isn't focused, request focus
		if (window.focus) {
			newwindow.focus();
		}
		return false;
	}

/*****************************************************************************
 * EOF
 *****************************************************************************/

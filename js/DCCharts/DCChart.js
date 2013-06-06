function DCChart(where, file){
	
	//Setup Ajax Object
	$.ajaxSetup({
		url:''+file,
		//type:'POST'
	});
	
	var jsres;
	
	//Call Ajax
	$.ajax({
		/*data: {
			'email':'empty'},*/
		/* code added by raj to show loading animation */
		beforeSend: function() {
			$('#graphDiv').hide();
			$('#loadingDiv').show();
		},
		success:function(d,s,x) {
			$('#loadingDiv').hide(); /* code added by raj to show loading animation */
			$('#graphDiv').show(); /* code added by raj to show loading animation */
			var jsObj = JSON.parse(d);
			//alert("json: "+jsObj.series[0].name);
			//-------- start declaring the chart
			chart = new Highcharts.Chart({
				chart: {
					renderTo: ''+where,
					defaultSeriesType: ''+jsObj.type,
					marginRight: 130,
					marginBottom: 45
				},
				title: {
					text: ''+jsObj.title,
					x: -20 //center
				},
				subtitle: {
					text: 'Source: DCNeighborhood.com',
					x: -20
				},
				/*tooltip: jsObj.tooltip,{
					formatter: function() {
							return this.percentage+" %";
					}
				},*/
				xAxis: {
					categories: jsObj.xAxis
				},
				
				plotOptions: {
					categories: jsObj.plotOptions
				},
				yAxis:jsObj.yAxis,
				legend: {
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'top',
					x: -10,
					y: 100,
					borderWidth: 0
				},
				series: jsObj.series
			});
		//--------------end chart declaration
		}
	});
}
jsBackend.sea =
{
	init: function()
	{
		// variables
		$chartSingleMetricPerDay = $('#chartSingleMetricPerDay');
		$chartDoubleMetricPerDay = $('#chartDoubleMetricPerDay');

		jsBackend.sea.charts.init();
		jsBackend.sea.chartSingleMetricPerDay.init();
		jsBackend.sea.chartDoubleMetricPerDay.init();
	}
}

jsBackend.sea.charts =
{
	init: function()
	{
		if($chartSingleMetricPerDay.length > 0)
		{
			Highcharts.setOptions(
			{
				colors: ['#ED561B', '#50b432', '#058DC7', '#EDEF00', '#24CBE5', '#64E572', '#FF9655'],
				title: {text: ''},
				legend:
				{
					layout: 'vertical',
					backgroundColor: '#FEFEFE',
					borderWidth: 0,
					shadow: false,
					symbolPadding: 12,
					symbolWidth: 10,
					itemStyle: {cursor: 'pointer', color: '#000', lineHeight: '18px'},
					itemHoverStyle: {color: '#666'},
					style: {right: '0', top: '0', bottom: 'auto', left: 'auto'}
				}
			});
		}
	}
}

jsBackend.sea.chartSingleMetricPerDay =
{
	chart: '',

	init: function()
	{
		if($chartSingleMetricPerDay.length > 0) {jsBackend.sea.chartSingleMetricPerDay.create();}
	},

	// add new chart
	create: function()
	{
		var xAxisItems = $('#dataChartSingleMetricPerDay ul.series ul.data li');
		var xAxisValues = [];
		var xAxisCategories = [];
		var counter = 0;
		var interval = Math.ceil(xAxisItems.length / 10);

		xAxisItems.each(function()
		{
			xAxisValues.push($(this).children('span.date').html());
			counter++;
		});

		var singleMetricName = $('#dataChartSingleMetricPerDay ul.series span.name').html();
		var singleMetricValues = $('#dataChartSingleMetricPerDay ul.series span.value');
		var singleMetricData = [];

		singleMetricValues.each(function() {singleMetricData.push(parseInt($(this).html()));});

		jsBackend.sea.chartSingleMetricPerDay.chart = new Highcharts.Chart(
		{
			chart: {renderTo: 'chartSingleMetricPerDay', margin: [60, 0, 30, 40], defaultSeriesType: 'area'},
			xAxis: {lineColor: '#CCC', lineWidth: 1, categories: xAxisValues, color: '#000'},
			yAxis: {title: {text: ''}},
			credits: {enabled: false},
			tooltip: {formatter: function() {return '<b>'+ this.series.name +'</b><br/>'+ xAxisValues[this.point.x] +': '+ this.y;}},
			plotOptions:
			{
				area: {marker: {enabled: false, states: {hover: {enabled: true, symbol: 'circle', radius: 5, lineWidth: 1}}}},
				column: {pointPadding: 0.2, borderWidth: 0},
				series: {fillOpacity: 0.3}
			},
			series: [{name: singleMetricName, data: singleMetricData}]
		});

	},

	// destroy chart
	destroy: function()
	{
		jsBackend.sea.chartSingleMetricPerDay.chart.destroy();
	}
}

jsBackend.sea.chartDoubleMetricPerDay =
{
	chart: '',

	init: function()
	{
		if($chartDoubleMetricPerDay.length > 0) {jsBackend.sea.chartDoubleMetricPerDay.create();}
	},

	// add new chart
	create: function()
	{
		var xAxisItems = $('#dataChartDoubleMetricPerDay ul.series li.serie:first-child ul.data li');
		var xAxisValues = [];
		var counter = 0;

		xAxisItems.each(function()
		{
			xAxisValues.push($(this).children('span.date').html());
			counter++;
		});

		var metric1Name = $('#dataChartDoubleMetricPerDay ul.series li#metric1serie span.name').html();
		var metric1Values = $('#dataChartDoubleMetricPerDay ul.series li#metric1serie span.value');
		var metric1Data = [];

		metric1Values.each(function() {metric1Data.push(parseFloat($(this).html()));});

		var metric2Name = $('#dataChartDoubleMetricPerDay ul.series li#metric2serie span.name').html();
		var metric2Values = $('#dataChartDoubleMetricPerDay ul.series li#metric2serie span.value');
		var metric2Data = [];

		metric2Values.each(function() {metric2Data.push(parseFloat($(this).html()));});


		var containerWidth = $('#chartDoubleMetricPerDay').width();

		jsBackend.sea.chartDoubleMetricPerDay.chart = new Highcharts.Chart(
		{
			chart: {renderTo: 'chartDoubleMetricPerDay', height: 400, width: containerWidth, margin: [60, 0, 30, 40], defaultSeriesType: 'line'},
			xAxis: {lineColor: '#CCC', lineWidth: 1, categories: xAxisValues, color: '#000'},
			yAxis: {title: {text: ''}},
			credits: {enabled: false},
			tooltip: {formatter: function() {return '<b>'+ this.series.name +'</b><br/>'+ xAxisValues[this.point.x] +': '+ this.y;}},
			plotOptions:
			{
				line: {marker: {enabled: false, states: {hover: {enabled: true, symbol: 'circle', radius: 5, lineWidth: 1}}}},
				area: {marker: {enabled: false, states: {hover: {enabled: true, symbol: 'circle', radius: 5, lineWidth: 1}}}},
				column: {pointPadding: 0.2, borderWidth: 0},
				series: {fillOpacity: 0.3}
			},
			series: [{name: metric1Name, data: metric1Data, type: 'area'}, {name: metric2Name, data: metric2Data}]
		});
	},

	// destroy chart
	destroy: function()
	{
		jsBackend.sea.chartDoubleMetricPerDay.chart.destroy();
	}
}

$(jsBackend.sea.init);

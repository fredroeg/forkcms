/**
 * All the settings to create the highchart
 *
 * @author	Frederick Roegiers <frederick.roegiers@wijs.be>
 */

jsFrontend.currency_converter =
{
    init : function ()
    {

        var chart;

        chart = new Highcharts.Chart({

            chart: {

                renderTo: 'linechart',

                type: window.graphDataObj.graphType,

                zoomType: 'x'

            },

            title: {

                text: window.graphDataObj.graphTitle

            },

            subtitle: {

                text: window.graphDataObj.graphSubtitle

            },

            xAxis: {
                title: {

                    text: window.graphDataObj.graphXaxistitle

                },

                categories: returnDates()


            },

            yAxis: {

                title: {

                    text: window.graphDataObj.graphYaxistitle

                }


            },

            legend: {

                layout: 'vertical',

                backgroundColor: '#FFFFFF',

                align: 'left',

                verticalAlign: 'top',

                x: 100,

                y: 70,

                floating: true,

                shadow: true

            },

            tooltip: {

                formatter: function() {

                    return ''+

                        this.x +': '+ this.y;

                }

            },

            plotOptions: {

                bar: {

                    dataLabels: {

                        enabled: true

                    }
                }

            },

                series: [{

                name: window.graphDataObj.graphCurrency,

                data: returnRates()

            }]

        });

        function returnRates()
        {
            var windowData = window.graphDataObj.rateValues;

            var numbers = [];
            for(var x in windowData)
            {
                var number = parseFloat(windowData[x]);
                numbers.push(number);
            }
            return(numbers);
        }

        function returnDates()
        {
            var windowData = window.graphDataObj.dateValues;

            var dates = [];
            for(var x in windowData)
            {
                var date = windowData[x];
                dates.push(date);
            }
            return(dates);
        }
    }
}

$(jsFrontend.currency_converter.init);
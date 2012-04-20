jsFrontend.currency_converter =
{
    init : function ()
    {
        var chart;

        chart = new Highcharts.Chart({

            chart: {

                renderTo: 'linechart',

                type: window.graphDataObj.graphType

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

                categories: returnKeys()


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

                data: returnNumbers()


            }]

        });

        function returnNumbers()
        {
            var windowData = window.graphDataObj.graphValues;

            var numbers = [];
            for(var x in windowData)
            {
                var number = parseFloat(windowData[x]);
                numbers.push(number);
            }
            return(numbers);
        }

        function returnKeys()
        {
            var windowData = window.graphDataObj.graphValues;
            return Object.keys(windowData);
        }

/*
function otherwayTemp()
{
    var key = '';
    var val = '';
    for (var p in window.data.value)
    {
        if (window.data.value.hasOwnProperty(p)) {
            key += p;
            val += window.data.value[p];
        }
    }
    alert (key + " " + val);
}
*/


    }
}

$(jsFrontend.currency_converter.init);
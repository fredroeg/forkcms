jsFrontend.currency_converter =
{
    init : function ()
    {
        var chart;

        chart = new Highcharts.Chart({

            chart: {

                renderTo: 'linechart',

                type: 'line'

            },

            title: {

                text: 'Evolution of a currency'

            },

            subtitle: {

                text: 'Compared to euro'

            },

            xAxis: {

                categories: returnKeys()

            },

            yAxis: {

                title: {

                    text: 'currency rate'

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

                name: 'US DOLLAR',

                data: returnNumbers()


            }]

        });

        function returnNumbers()
        {
            var windowData = window.data.value;
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

            var windowData = window.data.value;

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
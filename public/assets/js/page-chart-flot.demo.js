/*   
Template Name: Source Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 1.3.0
Author: Sean Ngu
Website: http://www.seantheme.com/source-admin-v1.3/admin/
*/

var primary		= '#2184DA',
    primaryLight= '#60A1DA',
    primaryDark	= '#1e77c5',
    info		= '#38AFD3',
    infoLight	= '#6FBDD5',
    infoDark	= '#2d8ca9',
    success		= '#17B6A4',
    successLight= '#59C2B7',
    successDark	= '#129283',
    warning		= '#fcaf41',
    warningLight= '#EEBD77',
    warningDark	= '#ca8c34',
    inverse		= '#3C454D',
    grey		= '#aab3ba',
    purple		= '#9b59b6',
    purpleLight	= '#BE93D0',
    purpleDark	= '#7c4792',
    danger      = '#F04B46';
    

var handleStackedChart = function () {
    "use strict";
    
    var d1 = [];
    for (var a = 0; a <= 5; a += 1) {
        d1.push([a, parseInt(Math.random() * 5)]);
    }
    var d2 = [];
    for (var b = 0; b <= 5; b += 1) {
        d2.push([b, parseInt(Math.random() * 5 + 5)]);
    }
    var d3 = [];
    for (var c = 0; c <= 5; c += 1) {
        d3.push([c, parseInt(Math.random() * 5 + 5)]);
    }
    var ticksLabel = [
        [0, "MON"], [1, "TUE"], [2, "WED"], [3, "THU"], [4, "FRI"], [5, "SAT"]
    ];

    var options = { 
        xaxis: {  tickColor: '#ddd',  ticks: ticksLabel, autoscaleMargin: 0.1},
        yaxis: {  tickColor: '#ddd'},
        grid: { 
            hoverable: true, 
            tickColor: "#ddd",
            borderWidth: 1,
            borderColor: '#ddd'
        },
        series: {
            stack: true,
            lines: { show: false, fill: false, steps: false },
            bars: { show: true, barWidth: 0.5, align: 'center', fillColor: null },
            highlightColor: 'rgba(0,0,0,0.8)'
        },
        legend: {
            show: true,
            labelBoxBorderColor: '#ccc',
            position: 'ne',
            noColumns: 1
        }
    };
    var xData = [
        {
            data: d1,
            color: inverse,
            label: 'Series 1',
            bars: {
                fillColor: inverse
            }
        },
        {
            data: d2,
            color: warning,
            label: 'Series 2',
            bars: {
                fillColor: warning
            }
        },
        {
            data: d3,
            color: danger,
            label: 'Series 3',
            bars: {
                fillColor: danger
            }
        }
    ];
    $.plot("#stacked-chart", xData, options);

    function showTooltip2(x, y, contents) {
        $('<div id="tooltip" class="flot-tooltip">' + contents + '</div>').css( {
            top: y,
            left: x + 35
        }).appendTo("body").fadeIn(200);
    }
    
    var previousXValue = null;
    var previousYValue = null;
    $("#stacked-chart").bind("plothover", function (event, pos, item) {
        if (item) {
            var y = item.datapoint[1] - item.datapoint[2];
    
            if (previousXValue != item.series.label || y != previousYValue) {
                previousXValue = item.series.label;
                previousYValue = y;
                $("#tooltip").remove();

                showTooltip2(item.pageX, item.pageY, y + " " + item.series.label);
            }
        }
        else {
            $("#tooltip").remove();
            previousXValue = null;
            previousYValue = null;       
        }
    });
};

var handleTrackingChart = function () {
    "use strict";
    
    var sin = [], cos = [];
    for (var i = 0; i < 14; i += 0.1) {
        sin.push([i, Math.sin(i)]);
        cos.push([i, Math.cos(i)]);
    }
    
    function updateLegend() {
        updateLegendTimeout = null;
        var pos = latestPosition;

        var axes = plot.getAxes();
        if (pos.x < axes.xaxis.min || pos.x > axes.xaxis.max ||
            pos.y < axes.yaxis.min || pos.y > axes.yaxis.max) {
            return;
        }
        var i, j, dataset = plot.getData();
        for (i = 0; i < dataset.length; ++i) {
            var series = dataset[i];

            for (j = 0; j < series.data.length; ++j) {
                if (series.data[j][0] > pos.x) {
                    break;
                }
            }
    
            var y, p1 = series.data[j - 1], p2 = series.data[j];
            if (p1 === null) {
                y = p2[1];
            } else if (p2 === null) {
                y = p1[1];
            } else {
                y = p1[1] + (p2[1] - p1[1]) * (pos.x - p1[0]) / (p2[0] - p1[0]);
            }

            legends.eq(i).text(series.label.replace(/=.*/, "= " + y.toFixed(2)));
        }
    }
    
    if ($('#tracking-chart').length !== 0) {
        var plot = $.plot($("#tracking-chart"),
        [ 
            { data: sin, label: "Series1", color: inverse, shadowSize: 0},
            { data: cos, label: "Series2", color: success, shadowSize: 0} 
        ], 
        {
            series: {
                lines: { show: true }
            },
            crosshair: { mode: "x", color: inverse },
            grid: { hoverable: true, autoHighlight: false, borderColor: '#ccc', borderWidth: 0 },
            xaxis: {   },
            yaxis: {  tickColor: '#ddd', min: -2, max: 2 },
            legend: {
                labelBoxBorderColor: '#ddd',
                backgroundOpacity: 0.4,
                color:'#fff',
                show: true
            }
        });
        
        var legends = $("#tracking-chart .legendLabel");
        legends.each(function () {
            $(this).css('width', $(this).width());
        });

        var updateLegendTimeout = null;
        var latestPosition = null;

        $("#tracking-chart").bind("plothover",  function (pos) {
            latestPosition = pos;
            if (!updateLegendTimeout) {
                updateLegendTimeout = setTimeout(updateLegend, 50);
            }
        });
    }
};

var handleBarChart = function () {
    "use strict";
    
    if ($('#bar-chart').length !== 0) {
        var data = [ ["January", 10], ["February", 8], ["March", 4], ["April", 13], ["May", 17] ];
        
        $.plot("#bar-chart", [ {data: data, color: primary} ], {
            series: {
                bars: {
                    show: true,
                    barWidth: 0.4,
                    align: 'center',
                    fill: true,
                    fillColor: primary
                }
            },
            xaxis: {
                mode: "categories",
                tickColor: '#ddd',
                font: {
                    size: 11,
                    lineHeight: 16,
                    style: "normal",
                    weight: "300",
                    family: "'Nunito', sans-serif",
                    color: "#30373e"
                },
                autoscaleMargin:0.1
            },
            yaxis: {
                min: 0,
                font: {
                    size: 11,
                    lineHeight: 16,
                    style: "normal",
                    weight: "300",
                    family: "'Nunito', sans-serif",
                    color: "#30373e"
                }
            },
            grid: {
                borderWidth: 1,
                borderColor: '#ddd'
            }
        });
    }
};

var handlePieChart = function () {
    "use strict";
    
    if ($('#pie-chart').length !== 0) {
        var data = [];
        var series = 3;
        var colorArray = [success, inverse, grey];
        for( var i = 0; i<series; i++)
        {
            data[i] = { label: "Series "+(i+1), data: Math.floor(Math.random()*100)+1, color: colorArray[i]};
        }
        
        $.plot($("#pie-chart"), data,
        {
            series: {
                pie: { 
                    show: true
                }
            },
            grid: {
                hoverable: true,
                clickable: true
            },
            legend: {
                labelBoxBorderColor: '#ddd',
                backgroundColor: 'none'
            }
        });
    }
};

var handleDonutChart = function () {
    "use strict";
    
    if ($('#donut-chart').length !== 0) {
        var data = [];
        var series = 3;
        var colorArray = [inverse, primary, grey];
        var nameArray = ['Series 1', 'Series 2', 'Series 3', 'Series 4', 'Series 5'];
        var dataArray = [20,14,12,31,23];
        for( var i = 0; i<series; i++)
        {
            data[i] = { label: nameArray[i], data: dataArray[i], color: colorArray[i] };
        }

        $.plot($("#donut-chart"), data, 
        {
            series: {
                pie: { 
                    innerRadius: 0.5,
                    show: true,
                    combine: {
                        color: '#999',
                        threshold: 0.2
                    }
                }
            },
            grid:{borderWidth:0, hoverable: true, clickable: true}
        });
    }
};

var handleLineChart = function () {
    "use strict";
    
    function showTooltip(x, y, contents) {
        $('<div id="tooltip" class="flot-tooltip">' + contents + '</div>').css( {
            top: y - 45,
            left: x - 55
        }).appendTo("body").fadeIn(200);
    }
    if ($('#line-chart').length !== 0) {
        var d1 = [[1, 35], [2,60], [3, 55], [4, 50], [5, 35], [6, 45],[7, 55], [8, 50], [9,75], [10, 80], [11, 65], [12, 80], [13, 60]];
        var d2 = [[1, 26], [2,13], [3, 18], [4, 35], [5, 23], [6, 18],[7, 35], [8, 24], [9,14], [10, 14], [11, 29], [12, 30], [13, 43]];

        $.plot($("#line-chart"), [
                {
                    data: d1, 
                    label: "Page Views", 
                    color: purple,
                    lines: { show: true, fill:false, lineWidth: 2 },
                    points: { show: true, radius: 3, fillColor: '#fff' },
                    shadowSize: 0
                }, {
                    data: d2,
                    label: 'Visitors',
                    color: inverse,
                    lines: { show: true, fill:false, lineWidth: 2, fillColor: '' },
                    points: { show: true, radius: 3, fillColor: '#fff' },
                    shadowSize: 0
                }
            ], 
            {
                xaxis: {  
                    tickColor: '#ddd',
                    tickSize: 2,
                    font: {
                        size: 11,
                        lineHeight: 16,
                        style: "normal",
                        weight: "300",
                        family: "'Nunito', sans-serif",
                        color: "#30373e"
                    }
                },
                yaxis: {  
                    tickColor: '#ddd', 
                    tickSize: 20,
                    font: {
                        size: 11,
                        lineHeight: 16,
                        style: "normal",
                        weight: "300",
                        family: "'Nunito', sans-serif",
                        color: "#30373e"
                    }
                },
                grid: { 
                    hoverable: true, 
                    clickable: true,
                    tickColor: "#ccc",
                    borderWidth: 1,
                    borderColor: '#ddd'
                }
            }
        );
        var previousPoint = null;
        $("#line-chart").bind("plothover", function (event, pos, item) {
            $("#x").text(pos.x.toFixed(2));
            $("#y").text(pos.y.toFixed(2));
            if (item) {
                if (previousPoint !== item.dataIndex) {
                    previousPoint = item.dataIndex;
                    $("#tooltip").remove();
                    var y = item.datapoint[1].toFixed(2);
            
                    var content = item.series.label + " " + y;
                    showTooltip(item.pageX, item.pageY, content);
                }
            } else {
                $("#tooltip").remove();
                previousPoint = null;            
            }
            event.preventDefault();
        });
    }
};

var handleLiveUpdatedChart = function () {
    "use strict";

    function update() {
        plot.setData([ getRandomData() ]);
        plot.draw();
        setTimeout(update, updateInterval);
    }
    function getRandomData() {
        if (data.length > 0) {
            data = data.slice(1);
        }

        while (data.length < totalPoints) {
            var prev = data.length > 0 ? data[data.length - 1] : 50;
            var y = prev + Math.random() * 10 - 5;
            if (y < 0) {
                y = 0;
            }
            if (y > 100) {
                y = 100;
            }
            data.push(y);
        }

        var res = [];
        for (var i = 0; i < data.length; ++i) {
            res.push([i, data[i]]);
        }
        return res;
    }
    
    if ($('#live-updated-chart').length !== 0) {
        var data = [], totalPoints = 150;

        var updateInterval = 1000;
        $("#updateInterval").val(updateInterval).change(function () {
            var v = $(this).val();
            if (v && !isNaN(+v)) {
                updateInterval = +v;
                if (updateInterval < 1) {
                    updateInterval = 1;
                }
                if (updateInterval > 2000) {
                    updateInterval = 2000;
                }
                $(this).val("" + updateInterval);
            }
        });

        var options = {
            series: { shadowSize: 0, color: warning, lines: { show: true, fill:true } }, // drawing is faster without shadows
            yaxis: { 
                min: 0, 
                max: 100, 
                tickColor: '#ddd',
                font: {
                    size: 11,
                    lineHeight: 16,
                    style: "normal",
                    weight: "300",
                    family: "'Nunito', sans-serif",
                    color: "#30373e"
                }
            },
            xaxis: { 
                show: true, 
                tickColor: '#ddd',
                font: {
                    size: 11,
                    lineHeight: 16,
                    style: "normal",
                    weight: "300",
                    family: "'Nunito', sans-serif",
                    color: "#30373e"
                }
            },
            grid: {
                borderWidth: 1,
                borderColor: '#ddd'
            }
        };
        var plot = $.plot($("#live-updated-chart"), [ getRandomData() ], options);

        update();
    }
};


/* Application Controller
------------------------------------------------ */
var PageDemo = function () {
	"use strict";
	
	return {
		//main function
		init: function () {
            handleLiveUpdatedChart();
            handleLineChart();
            handleBarChart();
            handlePieChart();
            handleStackedChart();
            handleTrackingChart();
            handleDonutChart();
		}
    };
}();
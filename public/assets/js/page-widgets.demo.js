/*   
Template Name: Source Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 1.3.0
Author: Sean Ngu
Website: http://www.seantheme.com/source-admin-v1.3/admin/
*/
    
var purple = '#9b59b6';
var purpleLight = '#BE93D0';
var purpleDark = '#7c4792';
var success = '#17B6A4';
var successDark = '#129283';
var primary = '#2184DA';
var primaryDark = '#1e77c5';
var info = '#38AFD3';
var inverse = '#3C454D';
var warning = '#fcaf41';
var danger = '#F04B46';
var dangerLight = '#F58A87';
var dangerDark = '#c03c38';
var lime = '#65C56F';
var grey = '#aab3ba';
var white = '#fff';
var fontFamily = '"Nunito", sans-serif';
var fontWeight = '300';
var fontStyle = 'normal';

var handleWidgetStatSparkline = function() {
    "use strict";
    
    var semiTransparent = 'rgba(0,0,0,0.3)';
    var semiWhiteTransparent = 'rgba(255,255,255,0.2)';
    var semiLimeTransparent = 'rgba(101, 197, 111, 0.7)';
    var semiBlueTransparent = 'rgba(33, 132, 218, 0.67)';
    var options = {
        height: '50px',
        width: '100%',
        fillColor: semiTransparent,
        lineWidth: 1.5,
        spotRadius: '0',
        highlightLineColor: semiTransparent,
        highlightSpotColor: semiTransparent,
        spotColor: false,
        minSpotColor: false,
        maxSpotColor: false
    };
    
    function renderWidgetStatSparkline() {
        var value = [20,30,45,40,45, 60, 70, 60, 50,40,35,40,50,70,90,40];
        options.width = '100%';
        options.type = 'line';
        options.height = '98px';
        options.lineColor = semiTransparent;
        options.lineColor = purpleDark;
        options.fillColor = purpleDark;
        options.highlightLineColor = semiTransparent;
        options.highlightSpotColor = semiTransparent;
        
        if ($('#sparkline-line-chart').length !== 0) {
            $('#sparkline-line-chart').sparkline(value, options);
        }
        
        var value = [50,30,45,40,50,90,20,35,40,50,70,40,80,70,60,50,40,60,40,90,50,30,50,40,30,20,50,68,92];
        options.barColor = primaryDark;
        options.barSpacing = 3;
        options.type= 'bar';
        options.barWidth = '10';
        
        if ($('#sparkline-bar-chart').length !== 0) {
            $('#sparkline-bar-chart').sparkline(value, options);
        }
    }

    renderWidgetStatSparkline();

    $(window).on('resize', function() {
        $('#sparkline-line-chart').empty();
        $('#sparkline-bar-chart').empty();
        renderWidgetStatSparkline();
    });
};

var handleFlotLineChart = function() {
    "use strict";
    
    var d1 = [[0, 3], [2, 8], [4, 5], [6, 13], [8, 11], [10, 15]];
    var targetChart = '#flot-line-chart';
    
    if ($(targetChart).length !== 0) {
        var targetHeight = $(targetChart).attr('data-height');
        $(targetChart).height(targetHeight);
        
        $.plot($(targetChart), [{ 
            label: 'data 1',  
            data: d1, 
            color: white, 
            shadowSize: 0 
        }], {
            series: {
                lines: { show: true, lineWidth: 3, zero: true },
                points: { show: false, fill: true, fillColor: '#fff' }
            },
            xaxis: {
                tickLength: 0,
                font: {
                    size: 12,
                    lineHeight: 16,
                    style: fontStyle,
                    weight: fontWeight,
                    family: fontFamily,
                    color: white
                }
            },
            yaxis: {
                tickColor: primaryDark,
                tickSize: 4,
                alignTicksWithAxis: true,
                tickDecimals: 0,
                font: {
                    size: 12,
                    lineHeight: 16,
                    style: fontStyle,
                    weight: fontWeight,
                    family: fontFamily,
                    color: white
                }
            },
            grid: {
                borderColor: primaryDark,
                borderWidth: 0
            },
            legend: {
                show: false
            }
        });
    }
};

var handleFlotPieChart = function() {
    "use strict";
    
    function labelFormatter(label, series) {
        return "<div style='font-size:11px; text-align:center; color:#fff;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
    }
    
    var d1 = [
        { label: 'Age 10+', data: 3, color: 'rgba(255,255,255,0.1)' }, 
        { label: 'Age 20+', data:15, color: 'rgba(255,255,255,0.2)' }, 
        { label: 'Age 30+', data: 7, color: 'rgba(255,255,255,0.3)' }, 
        { label: 'Age 40+', data: 8, color: 'rgba(255,255,255,0.4)' }
    ];
    var targetChart = '#flot-pie-chart';
    
    if ($(targetChart).length !== 0) {
        var targetHeight = $(targetChart).attr('data-height');
        $(targetChart).height(targetHeight);
        
        $.plot($(targetChart), d1 , {
            series: {
                pie: { 
                    show: true,
                    stroke: {
                        color: 'transparent'
                    },
                    radius: 1,
                    label: {
                        show: true,
                        radius: 2/3,
                        formatter: labelFormatter,
                        threshold: 0.1
                    }
                }
            },
            legend: {
                show: false
            }
        });
    }
}

var handleFlotBarChart = function() {
    "use strict";
    
    var targetChart = '#flot-bar-chart';
    var d1 = [[0, 3], [2, 8], [4, 5], [6, 13], [8, 11], [10, 15]];
    
    if ($(targetChart).length !== 0) {
        var targetHeight = $(targetChart).attr('data-height');
        $(targetChart).height(targetHeight);
        
        $.plot($(targetChart), [{ 
            label: 'data 1',  
            data: d1, 
            color: 'transparent', 
            shadowSize: 0 
        }], {
            series: {
                bars: { show: true, fillColor: white, barWidth: 1, align: 'center' }
            },
            xaxis: {
                tickLength: 0,
                font: {
                    size: 12,
                    lineHeight: 16,
                    style: fontStyle,
                    weight: fontWeight,
                    family: fontFamily,
                    color: white
                }
            },
            yaxis: {
                tickColor: 'rgba(0,0,0,0.2)',
                tickSize: 4,
                alignTicksWithAxis: true,
                tickDecimals: 0,
                font: {
                    size: 12,
                    lineHeight: 16,
                    style: fontStyle,
                    weight: fontWeight,
                    family: fontFamily,
                    color: white
                }
            },
            grid: {
                borderColor: primaryDark,
                borderWidth: 0
            },
            legend: {
                show: false
            }
        });
    }
};

var handleVisitorAnalyticsChart = function() {
    "use strict";
    
    var targetChart = '#flot-visitor-chart';
    var d1 = [];
    var d2 = [];
    var d3 = [];
    for (var i = 0; i <= 10; i += 1) {
        d1.push([i, parseInt(Math.random() * 30)]);
    }
    for (var i = 0; i <= 10; i += 1) {
        d2.push([i, parseInt(Math.random() * 30)]);
    }
    for (var i = 0; i <= 10; i += 1) {
        d3.push([i, parseInt(Math.random() * 30)]);
    }
    
    if ($(targetChart).length !== 0) {
        var targetHeight = $(targetChart).attr('data-height');
        $(targetChart).height(targetHeight);

        var options = {
            series: {
                curvedLines: {
                    apply: true,
                    active: true,
                    monotonicFit: true
                }
            },
            grid: {
                borderWidth: 0
            },
            legend: {
                show: false
            },
            xaxis: {
                font: {
                    size: 11,
                    lineHeight: 16,
                    style: fontStyle,
                    weight: fontWeight,
                    family: fontFamily,
                    color: inverse
                }
            },
            yaxis: {
                font: {
                    size: 11,
                    lineHeight: 16,
                    style: fontStyle,
                    weight: fontWeight,
                    family: fontFamily,
                    color: inverse
                }
            }
        };
        
        $.plot($(targetChart), [
               {data: d1, lines: { show: true, fill: true, fillColor: primary, shadow: false }, stack: true, color: primary },
               {data: d2, lines: { show: true, fill: true, fillColor: info, shadow: false }, stack: true, color: info },
               {data: d3, lines: { show: true, fill: true, fillColor: inverse, shadow: false }, stack: true, color: inverse }
        ], options);
    }
}

var handleBrowserChart = function() {
    "use strict";
    
    function labelFormatter(label, series) {
        return "<div style='font-size:11px; text-align:center; color:#fff;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
    }
    
    var d1 = [
        { label: 'Chrome', data: 15, color: success }, 
        { label: 'Safari', data:15, color: primary }, 
        { label: 'IE', data: 7, color: info }, 
        { label: 'Firefox', data: 8, color: warning }, 
        { label: 'Opera', data: 8, color: danger }
    ];
    var targetChart = '#flot-browser-chart';
        
    if ($(targetChart).length !== 0) {
        var targetHeight = $(targetChart).attr('data-height');
        $(targetChart).height(targetHeight);
        
        $.plot($(targetChart), d1 , {
            series: {
                pie: { 
                    innerRadius: 0.5,
                    show: true,
                    radius: 1,
                    label: {
                        show: true,
                        radius: 2.25/3,
                        formatter: labelFormatter,
                        threshold: 0.1
                    },
                    stroke: { 
                        width: 0.1
                    }
                }
            },
            legend: {
                show: false
            }
        });
    }
}

var handleVisitorsVectorMap = function() {
    "use strict";
    
    var targetMap = '#vector-map';
    
    if ($(targetMap).length !== 0) {
        var targetHeight = $(targetMap).attr('data-height');
        $(targetMap).height(targetHeight);
        
        var map = new jvm.WorldMap({
            map: 'world_merc_en',
            container: $(targetMap),
            normalizeFunction: 'linear',
            hoverOpacity: 0.5,
            hoverColor: false,
            markerStyle: {
                initial: {
                    stroke: '#fff',
                    r: 3
                }
            },
            regions: [{
                attribute: 'fill'
            }],
            regionStyle: {
                initial: {
                    fill: 'rgba(0,0,0,0.35)',
                    "fill-opacity": 1,
                    stroke: 'none',
                    "stroke-width": 2,
                    "stroke-opacity": 1
                },
                hover: {
                    "fill-opacity": 0.8
                },
                selected: {
                    fill: 'fff'
                },
                selectedHover: {
                }
            },
            series: {
                regions: [{
                values: {
                    IN:'rgba(0,0,0,0.75)',
                    US:'rgba(0,0,0,0.75)',
                    KR:'rgba(0,0,0,0.75)',
                    FR:'rgba(0,0,0,0.75)'
                }
                }]
            },
            focusOn: {
                x: 0.6,
                y: 0.5,
                scale: 3
            },
            backgroundColor: warning
        });
    }
};

var handleWidgetChat = function() {
    "use strict";
    
    $('[data-toggle="chat-detail"]').live('click', function(e) {
        e.preventDefault();
        $(this).closest('.widget-chat').addClass('widget-chat-detail-toggled');
    });
    $('[data-dismiss="chat-detail"]').live('click', function(e) {
        e.preventDefault();
        $(this).closest('.widget-chat').removeClass('widget-chat-detail-toggled');
    });
};

var handleWidgetReload = function() {
    "use strict";
    
    $('[data-click="widget-reload"]').live('click', function(e) {
        e.preventDefault();
    
        var targetWidget = $(this).closest('.widget');
        $(targetWidget).append('<div class="widget-loader"><span class="spinner-small">Loading...</span></div>');
    
        setTimeout(function() {
            $(targetWidget).find('.widget-loader').remove();
        }, 1500);
    });
};

var handleWidgetTodolist = function() {
    "use strict";

    $('[data-click="todolist-checkbox"]').live('click', function(e) {
        e.preventDefault();
    
        var targetCheckbox = $(this).closest('.checkbox').find('input[type="checkbox"]');
        var targetLi = $(this).closest('li');
    
        if ($(targetCheckbox).is(':checked')) {
            $(targetLi).removeClass('completed');
            $(targetCheckbox).prop('checked',false);
        } else {
            $(targetLi).addClass('completed');
            $(targetCheckbox).prop('checked',true);
        }
    });
};


/* Application Controller
------------------------------------------------ */
var PageDemo = function () {
	"use strict";
	
	return {
		//main function
		init: function () {
		    handleWidgetStatSparkline();
		    handleFlotLineChart();
		    handleFlotPieChart();
		    handleFlotBarChart();
		    handleVisitorAnalyticsChart();
		    handleBrowserChart();
		    handleVisitorsVectorMap();
		    handleWidgetChat();
		    handleWidgetReload();
		    handleWidgetTodolist();
		}
    };
}();
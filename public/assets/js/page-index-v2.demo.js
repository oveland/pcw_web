/*   
Template Name: Source Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 1.3.0
Author: Sean Ngu
Website: http://www.seantheme.com/source-admin-v1.3/admin/
*/


Chart.defaults.global.legend.display = false;
Chart.defaults.global.defaultFontColor = '#333';
Chart.defaults.global.defaultFontFamily = '"Nunito", sans-serif';

var handleRenderVisitorAnalyticsChart = function() {
    var targetContainer = '#chart-visitor-analytics';
    var targetHeight = ($(targetContainer).closest('.panel').hasClass('panel-expand')) ? $(targetContainer).closest('.panel-body').height() - 47 : $(targetContainer).attr('data-height');
    
    $(targetContainer).height(targetHeight);
    
    var ctx = document.getElementById('chart-visitor-analytics').getContext('2d');
    var gradient = ctx.createLinearGradient(0, 0, 0, 500);
        gradient.addColorStop(0, 'rgba(62, 71, 79, 0.3)');
    
    var lineChartData = {
        labels : ["January","February","March","April","May","June","July"],
        datasets : [
            {
                label: "Visitors",
                borderWidth: 2,
                pointBorderWidth: 2,
                pointRadius: 5,
                backgroundColor : gradient,
                borderColor : "#333",
                pointBackgroundColor : "#333",
                pointBorderColor : "#fff",
                pointHoverBackgroundColor : "#fff",
                pointHoverBorderColor : "#333",
                data : [100, 120, 150, 170, 180, 200, 160]
            }
        ]
    };

    visitorLineChart = new Chart(ctx, {
        type: 'line',
        data: lineChartData
    });
};

var handleDoughnutChart = function() {
    
    var ctx2 = document.getElementById('doughnut-chrome').getContext("2d");
    var ctx3 = document.getElementById('doughnut3').getContext("2d");
    var ctx4 = document.getElementById('doughnut4').getContext("2d");
    var ctx5 = document.getElementById('doughnut5').getContext("2d");

    var gradient2 = ctx2.createLinearGradient(0, 0, 0, 400);
    gradient2.addColorStop(0, 'rgba(72, 85, 99, 0.1)');   
    gradient2.addColorStop(1, 'rgba(41, 50, 60, 0.2)');

    var randomScalingFactor = function(){ return Math.round(Math.random()*100); };

    var data2 = {
        labels: ['Unique Visitor', 'Page Views', 'Total Page Views'],
        datasets: [{
            data: [50, 100, 150],
            backgroundColor: ['#9B59B6', '#8E44AD', '#66317C'],
            borderColor: ['#fff', '#fff', '#fff'],
            borderWidth: 2,
        }]
    };
    var data3 = {
        labels: ['Unique Visitor', 'Page Views', 'Total Page Views'],
        datasets: [{
            data: [50, 100, 150],
            backgroundColor: ['#3498DB', '#2980B9', '#1F5F89'],
            borderColor: ['#fff', '#fff', '#fff'],
            borderWidth: 2,
        }]
    };
    var data4 = {
        labels: ['Unique Visitor', 'Page Views', 'Total Page Views'],
        datasets: [{
            data: [50, 100, 150],
            backgroundColor: ['#E67E22', '#D35400', '#B34902'],
            borderColor: ['#fff', '#fff', '#fff'],
            borderWidth: 2,
        }]
    };
    var data5 = {
        labels: ['Unique Visitor', 'Page Views', 'Total Page Views'],
        datasets: [{
            data: [50, 100, 150],
            backgroundColor: ['#1ABC9C', '#16A085', '#0F6655'],
            borderColor: ['#fff', '#fff', '#fff'],
            borderWidth: 2,
        }]
    };
    
    new Chart(ctx2, {
        data: data2,
        type: 'doughnut'
    });
    new Chart(ctx3, {
        data: data3,
        type: 'doughnut'
    });
    new Chart(ctx4, {
        data: data4,
        type: 'doughnut'
    });
    new Chart(ctx5, {
        data: data5,
        type: 'doughnut'
    });
    /*
    new Chart(ctx5).Doughnut(data6, {
        animation: false,
        segmentStrokeWidth: 0.001,
        tooltipEvents: ['mousemove', 'touchstart', 'touchmove'],
        tooltipFillColor: 'rgba(0,0,0,0.8)',
        tooltipFontFamily: '"Nunito", sans-serif',
        tooltipFontSize: 11,
        tooltipFontStyle: '300',
        tooltipFontColor: '#fff',
        tooltipTitleFontFamily: '"Nunito", sans-serif',
        tooltipTitleFontSize: 11,
        tooltipTitleFontStyle: '300',
        tooltipTitleFontColor: '#fff',
        tooltipYPadding: 8,
        tooltipXPadding: 8,
        tooltipCaretSize: 5,
        tooltipCornerRadius: 3,
        customTooltips: function(tooltip) {
            var tooltipEl = $('#doughnut-safari-tooltip');

            if (!tooltip) {
                tooltipEl.hide();
                return;
            }

            tooltipEl.removeClass('above below');
            tooltipEl.addClass(tooltip.yAlign);
            tooltipEl.html('<div class="chartjs-tooltip-section">' + tooltip.text + '</div>');

            tooltipEl.css({
                display: 'block',
                left: tooltip.chart.canvas.offsetLeft + tooltip.x + 'px',
                top: tooltip.chart.canvas.offsetTop + tooltip.y + 'px',
                fontFamily: tooltip.fontFamily,
                fontSize: tooltip.fontSize,
                fontStyle: tooltip.fontStyle,
            });
        }
    });
    */
};

var handleDashboardGritterNotification = function() {
    setTimeout(function() {
        $.gritter.add({
            title: 'Welcome back, Admin!',
            text: 'You have 5 new notifications. Please check your inbox.',
            image: 'assets/img/user_profile.jpg',
            sticky: true,
            time: '',
            class_name: 'my-sticky-class'
        });
    }, 1000);
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


/* Application Controller
------------------------------------------------ */
var PageDemo = function () {
	"use strict";
	
	return {
		//main function
		init: function () {
            handleDoughnutChart();
            handleRenderVisitorAnalyticsChart();
		    handleDashboardGritterNotification();
		    handleWidgetReload();
		}
    };
}();
/*   
Template Name: Source Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 1.3.0
Author: Sean Ngu
Website: http://www.seantheme.com/source-admin-v1.3/admin/
*/


Chart.defaults.global.defaultFontColor = '#899096';

var randomScalingFactor = function() { 
    return Math.round(Math.random()*100)
};

var renderBarChart = function() {
    var barChartData = {
        labels : ['January','February','March','April','May','June','July','August','September','October','November','December'],
        datasets : [
            {
                borderWidth: 2,
                borderColor: '#30373e',
                backgroundColor: '#30373e',
                data : [randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor()],
                label: 'Total Item Sold'
            },{
                borderWidth: 2,
                borderColor: '#17B6A4',
                backgroundColor: '#17B6A4',
                data : [randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor()],
                label: 'Total Sales'
            }
        ]
    };
    var ctx2 = document.getElementById('monthly-report-chart').getContext('2d');
    var barChart = new Chart(ctx2, {
        type: 'bar',
        data: barChartData,
        options: {
            legend: {
                display: true
            },
            scales: {
                scaleLabel: {
                    fontColor: '#aab3ba'
                },
                gridLines: {
                    color: 'rgba(255,255,255,0.1)'
                }
            }
        }
    });
};

var renderDataTableSparkline = function() {
    $('[data-render="sparkline"]').each(function() {
        var randomScalingFactor = function() { 
            return Math.round(Math.random()*100)
        };
        var dataValue = [
            randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(),
            randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(),
            randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(),
            randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(),
            randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor()
        ];
        $(this).sparkline(dataValue, {
            type: 'line', 
            width: '100%', 
            height: '28px',
            fillColor: 'transparent', 
            spotColor: '#F04B46', 
            lineColor: '#17B6A4',
            minSpotColor: '#F04B46',
            maxSpotColor: '#F04B46',
            lineWidth: 1.5,
            spotRadius: 2
        });
    });
};

var handleDataTableDefault = function() {
    "use strict";

    if ($('#data-table').length !== 0) {
        $('#data-table').DataTable({
            responsive: true,
            "pageLength": 10,
            "lengthMenu": [[10, 20, -1], [10, 20, "All"]],
            "initComplete": function() {
                renderDataTableSparkline();
            }
        });
        $('#data-table').on( 'draw.dt', function () {
            renderDataTableSparkline();
        });
    }
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


/* Application Controller
------------------------------------------------ */
var PageDemo = function () {
	"use strict";
	
	return {
		//main function
		init: function () {
            renderBarChart();
		    handleDataTableDefault();
		    handleDashboardGritterNotification();
		}
    };
}();
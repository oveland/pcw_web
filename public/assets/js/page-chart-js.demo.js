/*   
Template Name: Source Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 1.3.0
Author: Sean Ngu
Website: http://www.seantheme.com/source-admin-v1.3/admin/
*/

var primary		        = '#2184DA',
    primaryTransparent  = 'rgba(33,132,218,0.15)',
    primaryLight	    = '#60A1DA',
    primaryDark	        = '#1e77c5',
    info		        = '#38AFD3',
    infoLight	        = '#6FBDD5',
    infoDark	        = '#2d8ca9',
    success		        = '#17B6A4',
    successTransparent	= 'rgba(23,182,264,0.15)',
    successLight	    = '#59C2B7',
    successDark	        = '#129283',
    warning		        = '#fcaf41',
    warningLight	    = '#EEBD77',
    warningDark	        = '#ca8c34',
    inverse		        = '#3C454D',
    inverseTransparent	= 'rgba(60,69,77,0.15)',
    grey		        = '#aab3ba',
    purple		        = '#9b59b6',
    purpleTransparent	= 'rgba(155,89,182,0.15)',
    purpleLight	        = '#BE93D0',
    purpleDark	        = '#7c4792',
    danger              = '#F04B46',
    white               = '#fff';

var randomScalingFactor = function() { 
    return Math.round(Math.random()*100)
};

var barChartData = {
    labels : ['January','February','March','April','May','June','July'],
    datasets : [
        {
            borderWidth: 2,
            borderColor: inverse,
            pointBackgroundColor: inverse,
            pointRadius: 2,
            backgroundColor: inverse,
            data : [randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor()]
        },
        {
            borderWidth: 2,
            borderColor: purple,
            pointBackgroundColor: purple,
            pointRadius: 2,
            backgroundColor: purple,
            data : [randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor()]
        }
    ]
};

var doughnutChartData = {
    labels: ['Grey', 'Green', 'Blue', 'Aqua', 'Black'],
    datasets: [{
        data: [300, 50, 100, 40, 120],
        backgroundColor: [grey, success, primary, info, inverse],
        borderColor: [white, white, white, white, white],
        borderWidth: 2,
        label: 'My dataset'
    }]
};

var lineChartData = {
    labels : ['January','February','March','April','May','June','July'],
    datasets : [{
        label: 'My First dataset',
        borderColor: inverse,
        borderWidth: 2,
        backgroundColor: inverseTransparent,
        pointBackgroundColor: inverse,
        pointHoverBackgroundColor: white,
        pointHoverBorderColor: inverse,
        pointBorderColor: white,
        pointBorderWidth: 1,
        pointRadius: 5,
        data : [randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor()]
    }, {
        label: 'My Second dataset',
        borderColor: success,
        borderWidth: 2,
        backgroundColor: successTransparent,
        pointBackgroundColor: success,
        pointHoverBackgroundColor: white,
        pointHoverBorderColor: success,
        pointBorderColor: white,
        pointBorderWidth: 1,
        pointRadius: 5,
        data : [randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor()]
    }]
};

var pieChartData = {
    labels: ['Green', 'Blue', 'Aqua', 'Grey', 'Black'],
    datasets: [{
        data: [300, 50, 100, 40, 120],
        backgroundColor: [success, primary, info, grey, inverse],
        borderColor: [white, white, white, white, white],
        borderWidth: 2,
        label: 'My dataset'
    }]
};

var polarChartData = {
    labels: ['Red', 'Orange', 'Green', 'Grey', 'Black'],
    datasets: [{
        data: [200, 50, 100, 40, 120],
        backgroundColor: [danger, warning, success, grey, inverse],
        borderColor: [white, white, white, white, white],
        borderWidth: 2,
        label: 'My dataset'
    }]
};

var radarChartData = {
    labels: ['Eating', 'Drinking', 'Sleeping', 'Designing', 'Coding', 'Cycling', 'Running'],
    datasets: [{
        label: 'My First dataset',
        borderWidth: 2,
        borderColor: inverse,
        pointBackgroundColor: inverse,
        pointBorderColor: white,
        pointHoverBackgroundColor: white,
        pointHoverBorderColor: inverse,
        pointBorderWidth: 1,
        pointRadius: 5,
        backgroundColor: inverseTransparent,
        data: [65,59,90,81,56,55,40]
    }, {
        label: 'My Second dataset',
        borderWidth: 2,
        borderColor: primary,
        pointBackgroundColor: primary,
        pointBorderColor: white,
        pointHoverBackgroundColor: white,
        pointHoverBorderColor: primary,
        pointBorderWidth: 1,
        pointRadius: 5,
        backgroundColor: primaryTransparent,
        data: [28,48,40,19,96,27,100]
    }]
};

Chart.defaults.global.legend.display = false;
Chart.defaults.global.defaultFontFamily = '"Nunito", sans-serif';
Chart.defaults.global.defaultFontStyle = 'normal';
Chart.defaults.global.defaultFontColor = inverse;

var handleGenerateGraph = function() {

    var ctx = document.getElementById('line-chart').getContext('2d');
    var lineChart = new Chart(ctx, {
        type: 'line',
        data: lineChartData
    });
    
    var ctx2 = document.getElementById('bar-chart').getContext('2d');
    var barChart = new Chart(ctx2, {
        type: 'bar',
        data: barChartData
    });
    
    var ctx3 = document.getElementById('radar-chart').getContext('2d');
    var radarChart = new Chart(ctx3, {
        type: 'radar',
        data: radarChartData
    });
    
    var ctx4 = document.getElementById('polar-area-chart').getContext('2d');
    var polarAreaChart = new Chart(ctx4, {
        type: 'polarArea',
        data: polarChartData
    });
    
    var ctx5 = document.getElementById('pie-chart').getContext('2d');
    window.myPie = new Chart(ctx5, {
        type: 'pie',
        data: pieChartData
    });
    
    var ctx6 = document.getElementById('doughnut-chart').getContext('2d');
    window.myDoughnut = new Chart(ctx6, {
        type: 'doughnut',
        data: doughnutChartData
    });
};


/* Application Controller
------------------------------------------------ */
var PageDemo = function () {
	"use strict";
	
	return {
		//main function
		init: function () {
            handleGenerateGraph();
		}
    };
}();
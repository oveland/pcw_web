/*   
Template Name: Source Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 1.3.0
Author: Sean Ngu
Website: http://www.seantheme.com/source-admin-v1.3/admin/
*/

var success = '#17B6A4',
    danger = '#ff5b57',
    primary = '#2184DA',
    warning = '#fcaf41',
    inverse = '#3C454D',
    info = '#38AFD3',
    lime = '#65C56F',
    grey = '#aab3ba',
    purple = '#9b59b6';

var renderSwitcher = function() {
    if ($('[data-render=switchery]').length !== 0) {
        $('[data-render=switchery]').each(function() {
            var themeColor = success;
            if ($(this).attr('data-theme')) {
                switch ($(this).attr('data-theme')) {
                    case 'danger':
                        themeColor = danger;
                        break;
                    case 'primary':
                        themeColor = primary;
                        break;
                    case 'purple':
                        themeColor = purple;
                        break;
                    case 'warning':
                        themeColor = warning;
                        break;
                    case 'info':
                        themeColor = info;
                        break;
                    case 'lime':
                        themeColor = lime;
                        break;
                    case 'grey':
                        themeColor = grey;
                        break;
                    case 'inverse':
                        themeColor = inverse;
                        break;
                }
            }
            var option = {};
                option.color = themeColor;
                option.secondaryColor = ($(this).attr('data-secondary-color')) ? $(this).attr('data-secondary-color') : '#dfdfdf';
                option.className = ($(this).attr('data-classname')) ? $(this).attr('data-classname') : 'switchery';
                option.disabled = ($(this).attr('data-disabled')) ? true : false;
                option.disabledOpacity = ($(this).attr('data-disabled-opacity')) ? $(this).attr('data-disabled-opacity') : 0.5;
                option.speed = ($(this).attr('data-speed')) ? $(this).attr('data-speed') : '0.5s';
            var switchery = new Switchery(this, option);
        });
    }
};

var checkSwitcherState = function() {
    $('[data-click="check-switchery-state"]').live('click', function() {
        alert($('[data-id="switchery-state"]').prop('checked'));
    });
    $('[data-change="check-switchery-state-text"]').live('change', function() {
        $('[data-id="switchery-state-text"]').text($(this).prop('checked'));
    });
};

var renderPowerRangeSlider = function() {
    if ($('[data-render="powerange-slider"]').length !== 0) {
        $('[data-render="powerange-slider"]').each(function() {
            var option = {};
                option.decimal = ($(this).attr('data-decimal')) ? $(this).attr('data-decimal') : false;
                option.disable = ($(this).attr('data-disable')) ? $(this).attr('data-disable') : false;
                option.disableOpacity = ($(this).attr('data-disable-opacity')) ? parseFloat($(this).attr('data-disable-opacity')) : 0.5;
                option.hideRange = ($(this).attr('data-hide-range')) ? $(this).attr('data-hide-range') : false;
                option.klass = ($(this).attr('data-class')) ? $(this).attr('data-class') : '';
                option.min = ($(this).attr('data-min')) ? parseInt($(this).attr('data-min')) : 0;
                option.max = ($(this).attr('data-max')) ? parseInt($(this).attr('data-max')) : 100;
                option.start = ($(this).attr('data-start')) ? $(this).attr('data-start') : null;
                option.step = ($(this).attr('data-step')) ? $(this).attr('data-step') : null;
                option.vertical = ($(this).attr('data-vertical')) ? $(this).attr('data-vertical') : false;
            if ($(this).attr('data-height')) {
                $(this).closest('.slider-wrapper').height($(this).attr('data-height'));
            }
            var switchery = new Switchery(this, option);
            var powerange = new Powerange(this, option);
        });
    }
};


/* Application Controller
------------------------------------------------ */
var PageDemo = function () {
	"use strict";
	
	return {
		//main function
		init: function () {
            renderSwitcher();
            checkSwitcherState();
            renderPowerRangeSlider();
		}
    };
}();
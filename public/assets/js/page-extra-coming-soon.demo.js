/*   
Template Name: Source Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 1.3.0
Author: Sean Ngu
Website: http://www.seantheme.com/source-admin-v1.3/admin/
*/


var handleCountDownTimer = function() {
    "use strict";
    
    var date = new Date();
    var year = date.getFullYear() + 1;
    
    $('#countdown').countdown(year + '/09/01', function(event) {
        var $this = $(this).html(event.strftime(''
          + '<div class="countdown-col"><div class="countdown-number"><span class="number">%d</span></div><div class="countdown-text">days</div></div>'
          + '<div class="countdown-col"><div class="countdown-number"><span class="number">%H</span></div><div class="countdown-text">hour</div></div>'
          + '<div class="countdown-col"><div class="countdown-number"><span class="number">%M</span></div><div class="countdown-text">minutes</div></div>'
          + '<div class="countdown-col"><div class="countdown-number"><span class="number">%S</span></div><div class="countdown-text">second</div></div>'));
    });
};


/* Application Controller
------------------------------------------------ */
var PageDemo = function () {
	"use strict";
	
	return {
		//main function
		init: function () {
		    handleCountDownTimer();
		}
    };
}();
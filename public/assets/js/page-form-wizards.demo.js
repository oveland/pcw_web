/*   
Template Name: Source Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 1.3.0
Author: Sean Ngu
Website: http://www.seantheme.com/source-admin-v1.3/admin/
*/

var handleBootstrapWizardsValidation = function() {
    "use strict";
    
    $("#wizard").bwizard({ 
        validating: function (e, ui) { 
            if ((ui.index == 0 && ui.nextIndex >= 0) || ui.nextIndex > 0) {
                // step-1 validation
                if (false === $('form[name="form-wizard"]').parsley().validate('wizard-step-1')) {
                    return false;
                }
            }
            if ((ui.index == 1 && ui.nextIndex >= 1) || ui.nextIndex > 1) {
                // step-2 validation
                if (false === $('form[name="form-wizard"]').parsley().validate('wizard-step-2')) {
                    return false;
                }
            }
            if ((ui.index == 2 && ui.nextIndex >= 2) || ui.nextIndex > 2) {
                // step-3 validation
                if (false === $('form[name="form-wizard"]').parsley().validate('wizard-step-3')) {
                    return false;
                }
            }
            if ((ui.index == 3 && ui.nextIndex >= 3) || ui.nextIndex > 3) {
                // step-4 validation
                if (false === $('form[name="form-wizard"]').parsley().validate('wizard-step-4')) {
                    return false;
                }
            }
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
		    handleBootstrapWizardsValidation();
		}
    };
}();
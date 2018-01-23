/*	Plugin para validar campos obligatorios (*) mediante labels con la clase field-required
 * 	Creado por: Oscar Velásquez
 * 	Para:
 * 	PCW Tecnología.
 * */

jQuery.fn.extend({
    isValid: function (showAlert) {
        showAlert = showAlert === undefined ? true : showAlert;
        var isValid = true;
        var el = $(this);
        $(el).find('.field-required:visible').each(function (i, e) {
            var labelFor = $(e).attr('for');
            if (labelFor) {
                if (!input_validate_parent(el, labelFor, $(e).text())) isValid = false;
            }
        });
        if (!isValid && showAlert) {
            gerror('Los campos con <b>*</b> son obligatorios');
        }
        return isValid;
    }
});

jQuery.fn.extend({
    isOnlyValid: function (e) {
        var isValid = true;
        var el = $(this);
        $(el).find('.field-required').each(function (i, e) {
            if (is_null($(e).val())) isValid = false;
        });
        if (!isValid) {
            gerror('No puede ingresar campos vacíos');
        }
        return isValid;
    }
});

var labelsForValidation = {}; // Variable global para almacenar el contienido de los labels de la vista

$(document).ready(function () {
    getLabelsView();
});

//*************************************************************************************************************

//Función para guardar todos los labels presentes en la vista actual
function getLabelsView() {
    $("label").each(function () {
        labelsForValidation[$(this).attr('for') + 'Label'] = $(this).text();
    });
}

//Función para determinar si un elemento es NO NULO
function is_not_null(element) {
    if (element == 'null' || element == null || element == undefined || element == '' || element.toString() == '0') {
        return false;
    }
    return true;
}

//Función para determinar si un elemento es NULO
function is_null(element) {
    return (is_not_null(element)) ? false : true;
}


//*************************************************************************************************************
//******************** VALIDACIÓN DE SELECTS CON LABELS ASOCIADOS *********************************************
//*************************************************************************************************************

//a.1). Función para determinar si la opción de un select es NO NULO. En caso NULO busca el label asociado y lo resalta con color ROJO:
function select_validate(element_id, text_validate) {
    return input_validate(element_id, text_validate);
}

//a.2). Función para determinar si la opción de un INPUT es NO NULO. En caso NULO busca el label asociado y lo resalta con color ROJO:
function input_validate(element_id, text_validate) {
    getLabelsView();
    // Evento para cuando el input cambie
    $('body').on('change', '#' + element_id + ', input[name="' + element_id + '"]', function () {
        if (is_not_null($(this).val())) {
            is_valid(this);
        }
    });
    // Validación y alerta:
    var elementValue = $("#" + element_id).val();
    if (elementValue == undefined) elementValue = $('input[name="' + element_id + '"]').val();
    if (is_not_null(elementValue)) {
        $("label[for='" + element_id + "']").css('color', '');
        return true;
    }
    else {
        $("label[for='" + element_id + "']").css('color', 'rgba(200, 20, 10, 1)').text('').fadeOut().html('<b>' + text_validate + '</b>').fadeIn();
        return false;
    }
}

//a.3). Igual que a.2) pero teniendo en cuenta elementos dentro de un contendor padre.
function input_validate_parent(parent, element_id, text_validate) {
    getLabelsView();
    // Evento para cuando el input cambie
    $('body').on('change', '#' + element_id + ', input[name="' + element_id + '"]', function () {
        if (is_not_null($(this).val())) {
            is_valid_parent(parent, this);
        }
    });

    // Validación y alerta:
    var elementValue = $(parent).find(" #" + element_id).val();
    if (elementValue == undefined) elementValue = $(parent).find('input[name="' + element_id + '"]').val();

    if (is_not_null(elementValue)) {
        $("label[for='" + element_id + "']").css('color', '');
        return true;
    }
    else {
        $("label[for='" + element_id + "']").css('color', 'rgba(200, 20, 10, 1)').text('').fadeOut().html('<b>' + text_validate + '</b>').fadeIn();
        return false;
    }
}

//b).Función para resstablecer el label de un elemento a su estado inicial
function is_valid(element) {
    var id = $(element).attr('id');
    if (id == undefined) id = $(element).attr('name');
    $("label[for='" + id + "']").css('color', '').html(labelsForValidation[id + 'Label']);
}

//b.1).Función para resstablecer el label de un elemento a su estado inicial con implementación en a.3)
function is_valid_parent(parent, element) {
    var id = $(parent).find(element).attr('id');
    if (id == undefined) id = $(parent).find(element).attr('name');
    $("label[for='" + id + "']").css('color', '').html(labelsForValidation[id + 'Label']);
}
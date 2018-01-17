/**
 * Created by Oscar on 30/04/2017.
 */

var loadingClass = 'disabled faa-tada animated';

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ajaxComplete(function(){
        $('.tooltips').tooltip();
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
    });

    $(document).ajaxError(function(event,request,settings){
        if( request.statusText == "Unauthorized" || request.status == 401 ){
            gerror('Acceso no autorizado o sesión caducada');
            location.reload();
        }
    });

    $('#datetimepicker-report, .datepicker').datepicker({
        format: "yyyy-mm-dd",
        todayBtn: "linked",
        language: "es",
        orientation: "bottom auto",
        daysOfWeekHighlighted: "0,6",
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true
    });

    $('.default-select2').select2();
    $('[data-toggle="tooltip"]').tooltip();
    $('.tooltips').tooltip();
});

function alert(message){
    $.gritter.add({
        title: 'Ups!',
        text: message,
        sticky: false,
        time: '6000',
        class_name: 'gritter-error'
    });
}

function alert_type(message,type){
    $.gritter.add({
        title: 'Información',
        text: message,
        sticky: false,
        time: '3000',
        class_name: 'gritter-'+type
    });
}

/* Alerte gritter */
function gsuccess( message ){
    toastr.success(message,"Información");
}

function gwarning( message ){
    toastr.warning(message);
}

function gerror( message ){
    toastr.error(message,"Ups!");
}

function hideSideBar(){
    $('.slimScrollDiv .sidebar-minify-btn').click();
}
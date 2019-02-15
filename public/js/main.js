$(document).ready(function () {
    if($.fn.modalmanager){
        $.fn.modalmanager.defaults.resize = true;
        $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
            '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
            '<div class="progress progress-striped active">' +
            '<div class="progress-bar progress-bar-danger" style="width: 100%;"></div>' +
            '</div>' +
            '</div>';
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        error: function (xhr, request, error) {
            if (xhr.statusText === "Unauthorized" || xhr.status === 401) {
                toastr["warning"]("Tu sesión ha caducado. Ingresa nuevamente tus credenciales", "Sesión caducada!");
                location.reload();
            }
        },
        complete: function () {
            $('.tooltip').remove();
            $('.tooltips').tooltip();
            App.initComponents();
        }
    });

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "positionClass": "toast-top-right",
        "onclick": null,
        "showDuration": "1000",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
});
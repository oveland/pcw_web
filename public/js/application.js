/**
 * Created by Oscar on 30/04/2017.
 */


let loadingClass = 'disabled faa-vertical animated';
let loading;

$(document).ready(function () {
    loading = $('#loading').html();
    $('.body-content').fadeIn(1000);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ajaxComplete(function () {
        $('.tooltips').tooltip({
            container: 'body'
        });
        $('[data-toggle="tooltip"]').tooltip({
            container: 'body'
        });
        $('[data-toggle="popover"]').popover();

        initPanelCollapse();

        $('.default-select2').select2({
            formatResult: select2OptionFormat,
            formatSelection: select2OptionFormat,
            escapeMarkup: function(m) { return m; }
        });
    });

    $(document).ajaxError(function (event, request, settings) {
        if (request.statusText == "Unauthorized" || request.status == 401) {
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

    $('.date-time-picker-report').datetimepicker({
        format: "YYYY-MM-DD HH:mm:ss",
    });

    function select2OptionFormat(option) {
        var originalOption = option.element;
        if ($(originalOption).data('html')) {
            return $(originalOption).data('html');
        }
        return option.text;
    }

    $('.default-select2').select2({
        formatResult: select2OptionFormat,
        formatSelection: select2OptionFormat,
        escapeMarkup: function(m) { return m; }
    });


    $('[data-toggle="tooltip"]').tooltip({
        container: 'body'
    });
    $('.tooltips').tooltip({
        container: 'body'
    });

    /* Ajax pagination */
    $('body').on('click', '.pagination a', function (event) {
        event.preventDefault();
        var paginator = $(this);
        var mainContainer = paginator.parents('.main-container');
        var form = $('.form-search-report');
        if (form.isValid()) {
            form.find('.btn-search-report').addClass(loadingClass);
            //mainContainer.slideUp(100);
            mainContainer.find('*').css('opacity', '0.5');
            $.ajax({
                url: paginator.attr('href'),
                success: function (data) {
                    mainContainer.empty().hide().html(data).fadeIn();
                },
                complete: function () {
                    form.find('.btn-search-report').removeClass(loadingClass);
                }
            });
        }
    }).on('click', '.nav-item', function(){
        $(this).addClass('active').addClass('active-animated');
    });

    let timmer = 1000;
    $('.active-animated').each(function (i, e) {
        setTimeout(() => {
            $(e).addClass('active');
        }, timmer += 400);
    });

    initPanelCollapse();
});

function initPanelCollapse() {
    $('.panel').find('.panel-body').addClass('collapse in');
    $('[data-click="panel-collapse"]').on('click',function(){
        $(this).parents('.panel').find('.panel-body').collapse('toggle');
    });
}

function alert(message) {
    gerror(message);
}

function alert_type(message, type) {
    $.gritter.add({
        title: 'Información',
        text: message,
        sticky: false,
        time: '3000',
        class_name: 'gritter-' + type
    });
}

/* Alerte gritter */
function gsuccess(message) {
    toastr.success(message, "Información");
}

function ginfo(message) {
    toastr.info(message, "Información");
}

function gwarning(message) {
    toastr.warning(message);
}

function gerror(message) {
    toastr.error(message, "Ups!");
}

function hideSideBar() {
    $('.menu-toggler.sidebar-toggler').click();
}
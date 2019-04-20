/**
 * Created by Oscar on 4/05/2017.
 */
$(document).ready(function () {
    $("#empresa").change(function (event) {
        $('#monitorear').bootstrapSwitch('state', false).change();
        $('#monitorear').bootstrapSwitch('disabled', true).change();
        if ($(this).val() != '111111' || $(this).val() != 111111) {
            setTimeout(function () {
                $('#monitorear').bootstrapSwitch('disabled', false).change();
                $('#monitorear').bootstrapSwitch('state', true).change();
            }, 500);
            idEmp = $("#empresa").find(':selected').val();
            $("#ruta").empty().append('<option value="0">Cargando...</option>');

            $.ajax({
                url:'http://www.pcwserviciosgps.com/pcw_mov/php/genera_select.php?id=' + idEmp + '&opc=40',
                crossDomain: true,
                success:function(data){
                    console.log(data);
                    $("#ruta").html(data);//.change();
                }
            });

            $('#number').attr('type',($(this).val() == 28)?'text':'number');
        } else {
            $('#monitorear').bootstrapSwitch('disabled', false).change();
            $("#ruta").empty().append('<option value="0">Seleccione Empresa</option>');
        }

        vehiclesShowFrameCounter = JSON.parse(localStorage.getItem("vehiclesShowFrameCounter" + $(this).val()));
        vehiclesShowFrameCounter = vehiclesShowFrameCounter === null ? {} : vehiclesShowFrameCounter;
        refreshFrameLog();

        centerMapSelectedCompany();
    });

    setTimeout(function () {
        $("#empresa").change();
        $("#ruta").empty().append('<option value="0">Seleccione Empresa</option>');
        /*$("#empresa").load('http://www.pcwserviciosgps.com/pcw_mov/php/genera_select.php?opc=18',function(){

        });*/
    }, 2000);
});
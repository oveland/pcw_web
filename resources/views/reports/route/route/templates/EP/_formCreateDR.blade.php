<div class="modal fade" id="modal-create-dr" style="background: rgba(0,0,0,0.59);">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header well m-b-5">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-times"></i>
                </button>
                <h4 class="modal-title text-center text-primary">
                    <span>
                        <i class="fa fa-retweet"></i> @lang('Add') @lang('round trip')
                        •
                        @lang('Vehicle') <span class="dr-vehicle-number"></span>
                    </span>
                </h4>
            </div>
            <div class="modal-body grid">
                <form id="form-add-dr" class="form" action="{{ route('route-ajax-action') }}">
                    <input type="hidden" name="option" value="addDR"/>
                    <input type="hidden" class="dr-vehicle-id" name="dr-vehicle-id" value=""/>
                    <div class="form-group">
                        <label for="dr-date" class="col-md-5 control-label field-required">@lang('Date')</label>
                        <div class="col-md-5">
                            <div class="input-group date dr-date">
                                <input name="dr-date" id="dr-date" type="text" class="form-control primary-filter" autocomplete="off" placeholder="yyyy-mm-dd" value="{{ $dateReport }} 00:00"/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dr-departure-time" class="col-md-5 control-label field-required">@lang('Departure time')</label>
                        <div class="col-md-5">
                            <div class="input-group date dr-departure-time">
                                <input name="dr-departure-time" id="dr-departure-time" type="text" class="form-control" autocomplete="off" placeholder="00:00" value=""/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dr-arrival-time" class="col-md-5 control-label field-required">@lang('Arrival time')</label>
                        <div class="col-md-5">
                            <div class="input-group date dr-arrival-time">
                                <input name="dr-arrival-time" id="dr-arrival-time" type="text" class="form-control" autocomplete="off" placeholder="00:00" value=""/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="dr-route" class="col-md-5 control-label field-required">@lang('Route')</label>
                        <div class="col-md-5">
                            <select name="dr-route-id" id="dr-route" data-with-all="true" data-with-none="true" class="default-select2 form-control col-md-12 primary-filter">
                                @include('partials.selects.routes', ['routes' => $activeRoutes])
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dr-sp-number" class="col-md-5 control-label">@lang('Spreadsheet number')</label>
                        <div class="col-md-5">
                            <div class="input-group">
                                <input type="number" name="dr-sp-number" id="dr-sp-number" class="form-control primary-filter" value="" min="0" max="100000000" maxlength="100000000">
                                <span class="input-group-addon">
                                    <i class="fa fa-file"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dr-sp-passengers" class="col-md-5 control-label">@lang('Spreadsheet passengers')</label>
                        <div class="col-md-5">
                            <div class="input-group">
                                <input type="number" name="dr-sp-passengers" id="dr-sp-passengers" class="form-control primary-filter" value="" min="0" max="100">
                                <span class="input-group-addon">
                                    <i class="fa fa-users"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dr-visual-passengers" class="col-md-5 control-label">@lang('Visual passengers')</label>
                        <div class="col-md-5">
                            <div class="input-group">
                                <input type="number" name="dr-visual-passengers" id="dr-visual-passengers" class="form-control primary-filter" value="" min="0" max="100">
                                <span class="input-group-addon">
                                    <i class="fa fa-eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group m-0">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-outline" data-dismiss="modal">
                                <i class="fa fa-save"></i> @lang('Cancel')
                            </button>
                            <button type="submit" class="btn green">
                                <i class="fa fa-save"></i> @lang('Save')
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    #modal-create-dr .form-group {
        display: flow-root;
    }

    #modal-create-dr .control-label {
        text-align: right;
        margin-top: 8px;
    }

    #modal-create-dr .grid {
        display: grid;
    }

    #modal-create-dr hr {
        margin: 10px 0;
        width: 100%;
        display: inline-flex;
    }
</style>

<script>
    $('.btn-add-dr').click(function() {
        $('#modal-create-dr').modal('show');

        const vehicleTab = $('.report-container .nav-pills > li.active > a');
        $('.dr-vehicle-number').html(vehicleTab.data('vehicle-number'));
        $('.dr-vehicle-id').val(vehicleTab.data('vehicle-id'));
    });

    initDateTimePicker("YYYY-MM-DD", $('.dr-date'));
    initDateTimePicker("HH:mm", $('.dr-departure-time, .dr-arrival-time'));

    const form = $('#form-add-dr');

    form.submit(function(e) {
        e.preventDefault();

        if(form.isValid()) {
            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        gsuccess(data.message);
                        $('#modal-create-dr').modal('hide');
                        setTimeout(function () {
                            $('.btn-search-report').click();
                        }, 1000);
                    } else {
                        gerror(data.message);
                    }
                },
                error: function () {
                    gerror('@lang('An error occurred in the process. Contact your administrator')')
                }
            });
        };
    });
</script>
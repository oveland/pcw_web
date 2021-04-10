@if($report->isNotEmpty())
    <div class="row" style="background: white">
        <div class=" panel-inverse col-md-12 col-sm-12 col-xs-12">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="{{ route('report.users.activity.search') }}?{{ $queryString }}&export=true" class="btn btn-lime bg-lime-dark btn-sm btn-rounded tooltips" data-title="@lang('Export excel')">
                        <i class="fa fa-file-excel-o"></i>
                    </a>
                </div>
                <p class="text-white label-vehicles">
                    <strong>
                        <i class="fa fa-list"></i> {{ $report->count() }} @lang('registers')
                    </strong>
                </p>
            </div>
            <div class="tab-content p-0">
                <div class="table-responsive">
                    <!-- begin table -->
                    <table class="table table-bordered table-striped table-hover table-valign-middle table-report">
                        <thead>
                        <tr class="inverse">
                            <th class="text-right" colspan="10">

                            </th>
                        </tr>
                        <tr class="inverse">
                            <th class="text-center">
                                <i class="fa fa-calendar" aria-hidden="true"></i> @lang('Date')
                            </th>
                            <th class="text-center">
                                <i class="fa fa-user" aria-hidden="true"></i> @lang('User')
                            </th>
                            <th class="text-center">
                                <i class="fa fa-link" aria-hidden="true"></i> @lang('Url name')
                            </th>
                            <th class="text-center">
                                <i class="fa fa-link" aria-hidden="true"></i> @lang('Method')
                            </th>
                            <th class="text-center">
                                <i class="fa fa-link" aria-hidden="true"></i> @lang('Category 1')
                            </th>
                            <th class="text-center">
                                <i class="fa fa-link" aria-hidden="true"></i> @lang('Category 2')
                            </th>
                            <th class="text-center">
                                <i class="fa fa-link" aria-hidden="true"></i> @lang('Category 3')
                            </th>
                            <th class="text-center">
                                <i class="fa fa-search" aria-hidden="true"></i> @lang('Params')
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($report as $log)
                            @php
                                $user = $log->user;
                            @endphp
                            <tr class="text-center">
                                <td class="">
                                    {{ $log->created_at }}
                                </td>
                                <td class="">
                                    @if($user)
                                        <span>{{ $user->username }}</span><br>
                                        <small>{{ $user->name }}</small>
                                    @endif
                                </td>
                                <td class="">
                                    {{ $log->route_name }}
                                </td>
                                <td class="text-center">
                                    {{ $log->method }}
                                </td>
                                <td class="text-center">
                                    {{ $log->category1 }}
                                </td>
                                <td class="text-center">
                                    {{ $log->category2 }}
                                </td>
                                <td class="text-center">
                                    {{ $log->category3 }}
                                </td>
                                <td class="">
                                    @if($log->params != '[]')
                                        <pre id="params-{{ $log->id }}" class="pre-params no-margin" style="display: none"></pre>
                                        <small class="text-primary text-bold" style="display: none">{{ $log->url }}</small>
                                        <button class="btn btn-circle btn-sm green btn-outline" onclick="prettyParams( '#params-{{ $log->id }}', '{{ $log->params }}')">
                                            <i class="fa fa-search-plus"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <!-- end table -->
                </div>
            </div>
        </div>
    </div>
@else
    @include('partials.alerts.noRegistersFound')
@endif

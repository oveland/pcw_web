    <template>
        <div class="">
            <div v-if="marks.length">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report">
                            <thead>
                            <tr class="inverse">
                                <th width="2%">
                                    <i class="fa fa-list-ol text-muted"></i>
                                </th>
                                <th width="10%">
                                    <i class="fa fa-retweet text-muted"></i><br> {{ $t('Trajectory') }}
                                </th>
                                <th width="15%">
                                    <i class="fa fa-clock-0 text-muted"></i><br> {{ $t('Time') }}
                                </th>
                                <th class="col-md-1">
                                    <i class="fa fa-users text-muted"></i><br> {{ $t('Passengers') }}
                                </th>
                                <th class="col-md-1">
                                    <i class="fa fa-dollar text-muted"></i><br> {{ $t('Total turn') }}
                                </th>
                                <th class="col-md-1">
                                    <i class="fa fa-dollar text-muted"></i><br> {{ $t('Subtotal') }}
                                </th>
                                <th class="col-md-1">
                                    <i class="fa fa-dollar text-muted"></i><br> {{ $t('Total dispatch') }}
                                </th>
                                <th class="col-md-1">
                                    <i class="fa fa-dollar text-muted"></i><br> {{ $t('Balance') }}
                                </th>
                                <th class="col-md-1">
                                    <i class="fa fa-print text-muted"></i><br> {{ $t('Print') }}
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="mark in marks" :set="turn = turnLiquidation(mark)">
                                <td class="text-center">{{ mark.number }}</td>
                                <td class="text-center hide">
                                    <i :class="mark.status.icon+' font-'+ mark.status.class" class="tooltips" data-placement="right" :data-original-title="mark.status.name"></i>
                                </td>
                                <td>
                                    <small class="span-full badge badge-info" v-if="mark.trajectory">
                                        {{ mark.trajectory.name }}
                                    </small>
                                </td>
                                <td width="15%" class="text-center">
                                    <small>{{ mark.initialTime }} - {{ mark.finalTime }}</small>
                                </td>
                                <td class="text-center">{{ mark.passengersBEA }}</td>
                                <td class="text-center">{{ turn.totalTurn | numberFormat('$0,0') }}</td>
                                <td class="text-center">{{ turn.subTotalTurn | numberFormat('$0,0') }}</td>
                                <td class="text-center">{{ turn.totalDispatch | thousandRound | numberFormat('$0,0') }}</td>
                                <td class="text-center">{{ turn.balance | thousandRound | numberFormat('$0,0') }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-tab btn-transparent btn-outline btn-circle tooltips" :title="$t('Print')" @click="exportTurnLiquidation(mark)" data-toggle="modal" data-target="#">
                                        <i class="fa fa-print"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right">
                                    <i class="icon-layers"></i> {{ $t('Total') }}
                                </td>
                                <td class="text-center">{{ totals.totalPassengersBea }}</td>
                                <td class="text-center">{{ totals.totalTurns | numberFormat('$0,0') }}</td>
                                <td class="text-center">{{ totals.subTotalTurns | numberFormat('$0,0') }}</td>
                                <td class="text-center">{{ totals.totalDispatch | thousandRound | numberFormat('$0,0') }}</td>
                                <td class="text-center">{{ totals.balance | thousandRound | numberFormat('$0,0') }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm yellow-crusta btn-tab btn-transparent btn-outline btn-circle tooltips" :title="$t('Print total')" @click="exportTotalLiquidation()" data-toggle="modal" data-target="#">
                                        <i class="fa fa-print"></i>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <modal name="modal-export-print" draggable="true" classes="vue-modal">
                    <div class="modal-header">
                        <button type="button" class="close" @click="closeExporter" aria-hidden="true"></button>
                        <h5 class="modal-title">
                            <i class="fa fa-image"></i> {{ $t('File other discount') }}
                        </h5>
                    </div>
                    <div class="moal-body">
                        <div class="col-md-8 col-md-offset-2 p-0 m-t-10 pdf-container">
                            <vue-friendly-iframe :src="linkToPrintLiquidation"></vue-friendly-iframe>
                        </div>
                    </div>
                </modal>
            </div>
        </div>
    </template>

    <script>
        import VModal from 'vue-js-modal';
        import VueFriendlyIframe from 'vue-friendly-iframe';

        Vue.use(VModal);

        export default {
            name: 'TakingsTurnsComponent',
            components:{
                VueFriendlyIframe,
            },
            props: {
                search: Object,
                marks: Array,
                totals: Object,
                urlExport: String,
                liquidationDetail: Object
            },
            data: function () {
                return {
                    urlParams: '',
                    exportLink: ''
                };
            },
            created(){
                this.exportLink = this.urlExport;
                console.log("exportLink >>> ", this.exportLink);
            },
            computed:{
                linkToPrintLiquidation: function () {
                    console.log(this.exportLink,this.urlParams);
                    return this.exportLink + '?' + this.urlParams;
                }
            },
            methods: {
                turnDiscounts: function (mark) {
                    let discounts = {
                        byMobilityAuxilio: 0,
                        byFuel: 0,
                        byOperativeExpenses: 0,
						byTolls: 0,
						byProvisions: 0,
                        total: 0
                    };

					_.each(mark.discounts, function (discount) {
						switch (discount.discount_type.uid) {
							case window.ml.discountTypes.auxiliary:
								discounts.byMobilityAuxilio = discount.value;
								break;
							case window.ml.discountTypes.fuel:
								discounts.byFuel = discount.value;
								break;
							case window.ml.discountTypes.operative:
								discounts.byOperativeExpenses = discount.value;
								break;
							case window.ml.discountTypes.toll:
								discounts.byTolls = discount.value;
								break;
							case window.ml.discountTypes.provisions:
								discounts.byProvisions = discount.value;
								break;
						}
						discounts.total += discount.value;
					});

                    return discounts;
                },
                turnLiquidation: function(mark){
                    const payFall = (Number.isInteger(mark.payFall) ? mark.payFall : 0);
                    const getFall = (Number.isInteger(mark.getFall) ? mark.getFall : 0);
                    const turnDiscounts = this.turnDiscounts(mark);
                    const totalTurn = mark.totalGrossBEA + mark.penalty.value;
                    const subTotalTurn = totalTurn - payFall  + getFall;
                    const totalDispatch = totalTurn - ( turnDiscounts.total - turnDiscounts.byFuel - turnDiscounts.byMobilityAuxilio) - mark.commission.value;
                    const balance = totalDispatch - payFall  + getFall - turnDiscounts.byFuel;

                    return {
                        payFall,
                        getFall,
                        turnDiscounts,
                        totalTurn,
                        subTotalTurn,
                        totalDispatch,
                        balance
                    };
                },
                closeExporter: function () {
                    console.log(this.exportLink);
                    this.$modal.hide('modal-export-print');
                },
                exportTurnLiquidation: function(mark) {
                    let turnLiquidation = this.turnLiquidation(mark);
                    turnLiquidation.vehicle = this.search.vehicle.id;
                    turnLiquidation.date = this.search.date;

                    console.log(turnLiquidation);
                    this.urlParams = this.serialize(turnLiquidation).toString();
                    console.log(this.urlParams);
                    this.$modal.show('modal-export-print');
                },
                exportTotalLiquidation: function(){
                    this.urlParams = '';
                },
                serialize: function(obj, prefix) {
                    let str = [], p;
                    for (p in obj) {
                        if (obj.hasOwnProperty(p)) {
                            let k = prefix ? prefix + "[" + p + "]" : p,
                                v = obj[p];
                            str.push((v !== null && typeof v === "object") ?
                                this.serialize(v, k) :
                                encodeURIComponent(k) + "=" + encodeURIComponent(v));
                        }
                    }
                    return str.join("&");
                }
            }
        }
    </script>

    <style>
        .pdf-container iframe{
            width: 100%;
            height: 600px;
        }
        .header-preview{
            font-size: 1.2em !important;
        }
    </style>
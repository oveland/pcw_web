<template>
    <div class="">
        <table class="table table-bordered table-condensed table-hover table-valign-middle table-report">
            <thead>
            <tr class="inverse">
                <th width="3%">
                    <i class="fa fa-list-ol text-muted"></i>
                </th>
                <th class="col-md-2">
                    <i class="fa fa-flag text-muted"></i><br> {{ $t('Route') }} / {{ $t('Trajectory') }}
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> {{ $t('Locks') }}
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> {{ $t('Auxiliaries') }}
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> {{ $t('Boarded') }}
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> {{ $t('BEA') }}
                </th>
                <th class="col-md-2">
                    <i class="fa fa-dollar text-muted"></i><br> {{ $t('Total BEA') }}
                </th>
                <th class="col-md-2">
                    <i class="fa fa-dollar text-muted"></i><br> {{ $t('Gross BEA') }}
                </th>
                <th class="col-md-3">
                    <i class="fa fa-dollar text-muted"></i><br> {{ $t('Discounts by turn') }}
                </th>
            </tr>
            </thead>
            <tbody>
            <tr class="" v-for="mark in marks">
                <td class="text-center">{{ mark.number }}</td>
                <td class="col-md-2 text-center">
                    <span>{{ mark.turn.route.name }}</span><br>
                    <span class="span-full badge badge-info" v-if="mark.trajectory">
                        {{ mark.trajectory.name }}
                    </span>
                    <span class="tooltips" :data-title="$t('Initial time')">{{ mark.initialTime }}</span> - <span class="tooltips" :data-title="$t('Final time')">{{ mark.finalTime }}</span>
                </td>
                <td class="text-center">{{ mark.locks }}</td>
                <td class="text-center">{{ mark.auxiliaries }}</td>
                <td class="text-center">{{ mark.boarded }}</td>
                <td class="text-center">{{ mark.passengersBEA }}</td>
                <td class="text-center">{{ mark.totalBEA | numberFormat('$0,0') }}</td>
                <td class="text-center">{{ mark.totalGrossBEA | numberFormat('$0,0') }}</td>
                <td class="text-center col-md-3">
                    <div v-for="discount in orderBy(mark.discounts, 'discount_type_id')" v-if="discount">
                        <span :data-original-title="discount.discount_type.description" class="tooltips">
                            <i :class="discount.discount_type.icon"></i> {{ discount.value | numberFormat('$0,0') }}
                        </span><br>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="9" style="height: 3px !important;background: gray;text-align: center;padding: 0;"></td>
            </tr>
            <tr class="totals">
                <td colspan="2" class="text-right">
                   <i class="icon-layers"></i> {{ $t('Totals') }}
                </td>
                <td class="text-center">{{ totals.totalLocks }}</td>
                <td class="text-center">{{ totals.totalAuxiliaries }}</td>
                <td class="text-center">{{ totals.totalBoarded }}</td>
                <td class="text-center">{{ totals.totalPassengersBea }}</td>
                <td class="text-center">{{ totals.totalBea | numberFormat('$0,0') }}</td>
                <td class="text-center">{{ totals.totalGrossBea | numberFormat('$0,0') }}</td>
                <td class="text-center">
                    <div v-for="totalDiscount in orderBy(liquidation.discountsByTurns, 'type_id')" v-if="totalDiscount">
                        <span :data-original-title="totalDiscount.discount.discount_type.description" class="tooltips">
                            <i :class="totalDiscount.discount.discount_type.icon"></i> {{ totalDiscount.value | numberFormat('$0,0') }}
                        </span><br>
                    </div>
                </td>
            </tr>
            <tr class="total-discount-by-turn">
                <td colspan="8" class="text-right">
                <span class="text-bold">
                    <i class="icon-tag"></i> {{ $t('Total Discount by turns') }}
                </span>
                </td>
                <td class="text-center">
                <span :title="$t('Total discount')" class="text-bold tooltips">
                    {{ totals.totalDiscountsByTurns | numberFormat('$0,0') }}
                </span><br>
                </td>
            </tr>

            <tr class="" v-for="otherDiscount in liquidation.otherDiscounts">
                <td colspan="8" class="text-right">
                <div class="text-bold col-lg-12 col-md-12 col-sm-12 col-xs-12 pull-right p-r-0">

                    <div class="input-group">
                        <span v-if="!readonly" class="input-group-addon faa-parent animated-hover tooltips" data-placement="bottom" :data-title="$t('Delete')" @click="removeOtherDiscount(otherDiscount.id)">
                            <i class="fa fa-trash faa-shake font-red"></i>
                        </span>
                        <div class="input-icon">
                            <i class="icon-tag font-green"></i> <input type="text" :disabled="readonly" class="form-control input-sm" :placeholder="$t('Description')" v-model="otherDiscount.name" @keyup="control.enableSaving = true">
                        </div>
                        <span v-if="otherDiscount.hasFile" class="input-group-addon faa-parent animated-hover tooltips" data-placement="left" :data-title="$t('Show file')" data-toggle="modal" data-target="#modal-show-file-discount" @click="showImagePreview(otherDiscount)">
                            <i class="fa fa-image faa-shake font-red"></i>
                        </span>
                        <span v-if="!readonly" class="input-group-addon">
                            <input type="file" accept="image/*" class="" @change="addDiscountFile(otherDiscount)">
                        </span>
                    </div>

                </div>
                </td>
                <td class="text-center">
                    <div class="input-icon">
                        <i class="fa fa-dollar font-green"></i> <input type="number" :disabled="readonly" class="form-control input-sm" :placeholder="$t('Discount')" v-model.number="otherDiscount.value">
                    </div>
                </td>
            </tr>

            <tr v-if="!readonly" class="">
                <td colspan="9" class="text-right">
                    <button v-if="control.enableSaving && !control.creating" class="btn btn-sm green btn-outline-light btn-outline btn-white" @click="$emit('update-liquidation')">
                        <i class="fa fa-plus"></i> {{ $t('Save') }}
                    </button>
                    <button class="btn btn-sm btn-outline btn-white" @click="addOtherDiscount()">
                        <i class="fa fa-plus"></i> {{ $t('Add') }}
                    </button>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="form form-horizontal total-discount">
            <hr class="hr">
            <div class="form-group">
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12 text-right">
                    <span class="text-bold">
                        <i class="icon-tag"></i> {{ $t('Total discounts') }}:
                    </span>
                </div>
                <div class="col-md-3 col-lg-3 col-sm-3 col-xs-12 text-right">
                    <span class="text-bold">{{ totals.totalDiscounts | numberFormat('$0,0') }}</span>
                </div>
            </div>
        </div>

        <modal name="modal-show-file-discount" draggable="true" classes="vue-modal">
            <div class="modal-header">
                <button type="button" class="close" @click="hide" aria-hidden="true"></button>
                <h5 class="modal-title">
                    <i class="fa fa-image"></i> {{ $t('File other discount') }}
                </h5>
                <div v-if="imagePreview" class="col-md-12 p-10 text-center">
                    <img :src="imagePreview" class="uploading-image" />
                </div>
            </div>
        </modal>

    </div>
</template>

<script>
    import Vue2Filters from 'vue2-filters';
    import VModal from 'vue-js-modal';

    Vue.use(VModal);

    export default {
        name: "DiscountComponent",
        mixins: [Vue2Filters.mixin],
        data: function () {
            return {
                imagePreview: null
            }
        },
        props: {
            readonly: Boolean,
            marks: Array,
            liquidation: Object,
            urlUpdateLiquidate: String,
            totals: Object,
            control: Object
        },
        methods: {
            show () {
                this.$modal.show('modal-show-file-discount');
            },
            hide () {
                this.$modal.hide('modal-show-file-discount');
            },
            addDiscountFile(otherDiscount){
                const file = event.target.files[0];
                otherDiscount.hasFile = !!file;
                otherDiscount.fileUrl = null;

                if(file){
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = e => {
                        otherDiscount.fileUrl = e.target.result;
                        this.control.enableSaving = true;
                        setTimeout(() => {
                            $('.tooltips').tooltip();
                        }, 500);
                    }
                }
            },
            showImagePreview(otherDiscount){
                this.imagePreview = otherDiscount.fileUrl;
                this.$modal.show('modal-show-file-discount');
            },
            addOtherDiscount: function () {
                const otherDiscountId = (new Date).getTime();

                this.liquidation.otherDiscounts.push({
                    id: otherDiscountId,
                    name: '',
                    value: '',
                    fileUrl: null,
                    hasFile: false
                });

                setTimeout(() => {
                    $('.tooltips').tooltip();
                }, 500);
                this.control.enableSaving = true;
            },
            saveOtherDiscounts: function(){
                this.$emit('update-liquidation');
            },
            removeOtherDiscount: function (idToRemove) {
                this.liquidation.otherDiscounts = _.filter(this.liquidation.otherDiscounts, function (other) {
                    return other.id !== idToRemove;
                });
                this.control.enableSaving = true;
            }
        }
    }
</script>

<style scoped>
    .uploading-image{
        width: 100%;
        border-radius: 5px;
    }

    .totals span {
        font-size: 1.1em !important;
    }

    .total-discount-by-turn span {
        font-size: 1.2em !important;
    }

    .total-discount span {
        font-size: 1.6em !important;
    }

    .input-icon > i {
        margin: 8px 2px 4px 10px !important;
    }
</style>
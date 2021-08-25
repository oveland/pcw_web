<template>
	<div class="col-md-12 bg-white" style="z-index: 1 !important;">
		<div v-if="search.vehicle.id" class="col-md-6 col-md-offset-3">
			<div class="col-md-3">
				<div class="form-group">
					<label for="advanceTakings">{{ $t('Takings') }}</label>
					<div class="input-group">
						<input id="advanceTakings" type="number" min="0" class="form-control" @keypress.enter="setAdvances" onFocus="this.select()" :placeholder="0" v-model.number="liquidation.advances.takings">
					</div>
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label for="advancePayFall">
						<i class='fa fa-angle-double-right font-green'></i> {{ $t('Pay fall') }}
					</label>
					<div class="input-group">
						<input id="advancePayFall" type="number" min="0" class="form-control" onFocus="this.select()" @keypress.enter="setAdvances" :placeholder="0" v-model.number="liquidation.advances.payFall">
					</div>
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label for="advanceGetFall">
						<i class='fa fa-angle-double-left font-yellow'></i> {{ $t('Get fall') }}
					</label>
					<div class="input-group">
						<input id="advanceGetFall" type="number" min="0" class="form-control" onFocus="this.select()" @keypress.enter="setAdvances" :placeholder="0" v-model.number="liquidation.advances.getFall">
					</div>
				</div>
			</div>

			<div class="col-md-12 divider"></div>

			<div class="col-md-9 text-center">
				<button class="btn btn-circle purple text-purple btn-outline f-s-13 uppercase" @click="setAdvances">
					<i class="fa fa-save"></i> {{ $t('Save')}}
				</button>
			</div>
		</div>
		<div v-else class="row">
			<div class="alert alert-warning alert-bordered m-b-10 mb-10 mt-10 col-md-4 col-md-offset-4 offset-md-4">
				<div class="col-md-2" style="padding-top: 10px">
					<i class="fa fa-3x fa-exclamation-triangle"></i>
				</div>
				<div class="col-md-10">
					<span class="close pull-right" data-dismiss="alert">×</span>
					<div style="margin-top: 10px">
						<strong>{{ $t('Select a vehicle') }}</strong>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import Swal from 'sweetalert2/dist/sweetalert2.min'

export default {
	props: {
		urlSetAdvance: String,
		search: Object,
		liquidation: Object
	},
	data(){
		return {
			control: {
				enableSaving: false,
				processing: false,
				canUpdate: false
			},
		}
	},
	methods: {
		setAdvances: function () {
			this.liquidation.advances.takings = this.liquidation.advances.takings ? this.liquidation.advances.takings : 0;
			this.liquidation.advances.payFall = this.liquidation.advances.payFall ? this.liquidation.advances.payFall : 0;
			this.liquidation.advances.getFall = this.liquidation.advances.getFall ? this.liquidation.advances.getFall : 0;

			App.blockUI({target: '#table-liquidations', animate: true});

			Swal.fire({
				title: this.$t('Processing'),
				text: this.$t('Saving advance'),
				onBeforeOpen: () => {
					Swal.showLoading();
				},
				heightAuto: true,
				allowOutsideClick: false,
				allowEscapeKey: false,
				showConfirmButton: false
			});
			this.control.processing = true;

			axios.post(this.urlSetAdvance.replace('ID', this.search.vehicle.id), {
				advances: this.liquidation.advances
			}).then(response => {
				const data = response.data;
				if( data.success ){
					this.control.enableSaving = false;
					gsuccess(data.message);
					this.liquidation.advances = data.advances;
				}else{
					gerror(data.message);
				}
			}).catch( (error) => {
				gerror(this.$t('Error setting advance'));
				console.log(error);
			}).then( () => {
				App.unblockUI('#table-liquidations');
				this.control.processing = false;
				Swal.close();
			});
		},
	}
}
</script>

<style scoped>

</style>
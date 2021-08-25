<template>
	<div>
		<div class="tabbable-custom hidden-lg hidden-md">
			<ul class="nav nav-tabs ">
				<li class="active">
					<a :href="`#step-discounts-${module}`" data-toggle="tab" aria-expanded="true"> <i class="icon-tag"></i> <span class="hidden-xs">{{ $t('Discounts') }}</span> </a>
				</li>
				<li class="">
					<a :href="`#step-penalties-${module}`" data-toggle="tab" aria-expanded="false"> <i class="icon-shield"></i> <span class="hidden-xs">{{ $t('Penalties') }}</span> </a>
				</li>
				<li class="">
					<a :href="`#step-commissions-${module}`" data-toggle="tab" aria-expanded="false"> <i class=" icon-user-follow"></i> <span class="hidden-xs">{{ $t('Commissions') }}</span> </a>
				</li>
				<li class="">
					<a :href="`#step-liquidate-${module}`" data-toggle="tab" aria-expanded="false"> <i class="icon-layers"></i> <span class="hidden-xs">{{ $t('Liquidation') }}</span> </a>
				</li>
			</ul>
		</div>

		<div class="mt-element-step hidden-sm hidden-xs">
			<div class="row step-line">
				<div class="phases col-md-3 mt-step-col first phase-inventory warning"
					 data-toggle="tab" :href="`#step-discounts-${module}`" data-active="warning">
					<div class="mt-step-number bg-white">
						<i class="icon-tag"></i>
					</div>
					<div class="mt-step-title uppercase font-grey-cascade">{{ $t('Discounts') }}</div>
					<div class="mt-step-content font-grey-cascade hide"></div>
				</div>
				<div class="phases col-md-3 mt-step-col phase-inventory" data-toggle="tab"
					 :href="`#step-penalties-${module}`" data-active="error">
					<div class="mt-step-number bg-white">
						<i class="icon-shield"></i>
					</div>
					<div class="mt-step-title uppercase font-grey-cascade">{{ $t('Penalties') }}</div>
					<div class="mt-step-content font-grey-cascade hide"></div>
				</div>
				<div class="phases col-md-3 mt-step-col phase-inventory" data-toggle="tab"
					 :href="`#step-commissions-${module}`" data-active="active">
					<div class="mt-step-number bg-white">
						<i class=" icon-user-follow"></i>
					</div>
					<div class="mt-step-title uppercase font-grey-cascade">{{ $t('Commissions') }}</div>
					<div class="mt-step-content font-grey-cascade hide"></div>
				</div>
				<div class="phases col-md-3 mt-step-col last phase-inventory" data-toggle="tab"
					 :href="`#step-liquidate-${module}`" data-active="done">
					<div class="mt-step-number bg-white">
						<i class="icon-layers"></i>
					</div>
					<div class="mt-step-title uppercase font-grey-cascade">{{ $t('Liquidation') }}</div>
					<div class="mt-step-content font-grey-cascade"></div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	props: ['module']
}
</script>

<style scoped>

</style>
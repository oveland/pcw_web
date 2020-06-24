<template>
    <div class="photo-details" v-if="photo.details">
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="photo">
                <p>
                    <span class="title">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <span class="detail">({{ photo.id }}) | {{ photo.details.date }}</span>
                </p>
            </div>
            <div v-if="photo.details.dispatchRegister" class="route">
                <p>
                    <span class="title">
                        <i class="fa fa-flag"></i>
                    </span>
                    <span class="detail">{{ photo.details.dispatchRegister.route.name }}</span>,
                    <span class="detail">{{ $t('Round trip')}} {{ photo.details.dispatchRegister.round_trip }}</span>,
                    <span class="detail">{{ $t('Turn') }} {{ photo.details.dispatchRegister.turn }}</span>
                </p>
            </div>
            <div class="passengers">
                <p class="detail">
                    {{ $t('Passengers') }}: <span v-if="photo.details.occupation">{{ photo.details.occupation.count }}</span>
                </p>
                <p class="detail">
                    {{ $t('Occupation') }}: <span v-if="photo.details.occupation">{{ ((40/100)*photo.details.occupation.count) | numberFormat('0.0') }}%</span>
                </p>
            </div>
        </div>

        <div class="col-md-6 col-sm-6 col-xs-12 text-right" style="">
            <div class="photo">
                <p>
                    <span class="title">
                        <i class="fa fa-users"></i>
                    </span>
                    <span class="detail">{{ $t('Counts') }}</span>
                </p>
            </div>
            <div class="passengers" v-if="photo.passengers">
                <ul>
                    <li v-for="roundTrip in photo.passengers.byRoundTrips" class="detail">
                        <p v-show="roundTrip.number">
                            <small>
                                <i class="fa fa-exchange"></i> {{ roundTrip.number }}, {{ roundTrip.route }}: {{ roundTrip.count }}
                            </small>
                        </p>
                    </li>
                </ul>
                <p class="detail">
                    <small>{{ $t('Total') }}: {{ photo.passengers.total }}</small>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "PhotoDetailsComponent",
        props: {
            photo: Object
        }
    }
</script>

<style scoped>
    .photo-details {
        display: flex;
        position: relative;
        height: 0;
        width: 100%;
        z-index: 100;
    }

    .photo-details > div {
        background: #1e2d3361;
        padding: 10px;
        height: 100px;
    }

    .photo-details p{
        margin: 0;
    }

    .photo-details .photo p {
        display: block;
        color: white;
        padding: 2px;
        font-size: 0.9em !important;
    }

    .photo-details .route p {
        display: block;
        color: white;
        padding: 2px;
        font-size: 0.8em !important;
    }

    .photo-details .passengers {
        padding: 0;
    }

    .photo-details .passengers p {
        color: white;
        padding: 2px;
        font-size: 0.8em !important;
    }
</style>
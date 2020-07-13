<template>
    <div class="photo-details" v-if="photo.details">
        <div>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="photo">
                    <p>
                    <span class="title">
                        <i class="fa fa-calendar"></i>
                    </span>
                        <span class="detail">{{ photo.details.date }} | ({{ photo.id }})</span>
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
                        {{ $t('Bearding passengers') }}: <span v-if="photo.details.occupation && photo.details.occupation.count">{{ photo.details.occupation.persons }}</span>
                        <span v-if="!photo.details.occupation.count">{{ $t('Paused count') }}</span>
                    </p>
                    <p class="detail">
                        {{ $t('Seating') }}: <span v-if="photo.details.occupation">{{ photo.details.occupation.seatingOccupiedStr }}</span>
                    </p>
                    <p class="detail text-bold text-uppercase"
                       :class="`percent-level-${photo.details.occupation.percentLevel}`">
                        {{ $t('Occupation') }}: <span v-if="photo.details.occupation && photo.details.occupation.count">{{ photo.details.occupation.percent | numberFormat('0.0') }}%</span>

                        <span class="text-danger text-uppercase" v-if="photo.alarms && photo.alarms.lockCamera">
                            <i class="fa fa-warning"></i> {{ $t('Lock alarm') }} <small style="font-size: 0.5em">{{ photo.alarms.counterLockCamera }}</small>
                        </span>
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-sm-6 col-xs-12 text-right" style="">
                <div class="photo">
                    <p>
                    <span class="title">
                        <i class="fa fa-users"></i>
                    </span>
                        <span class="detail">{{ photo.passengers.totalSum2 }} {{ $t('Counts') }}</span>
                    </p>
                </div>
                <div class="passengers" v-if="photo.passengers">
                    <ul v-if="photo.passengers.byRoundTrips.length">
                        <li v-for="roundTrip in photo.passengers.byRoundTrips" class="detail" v-if="roundTrip.number">
                            <p v-show="roundTrip.number">
                                <small>
                                    <i class="fa fa-exchange"></i> {{ roundTrip.number }}, {{ roundTrip.route }}: {{
                                    roundTrip.count }}
                                </small>
                            </p>
                        </li>
                    </ul>
                    <p class="detail">
                        <small>{{ $t('Total by round trips') }}: {{ photo.passengers.total }}</small>
                    </p>
                </div>
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
        width: 100%;
        background: #1e2d33bd;
        padding: 10px;
        height: auto;
        display: inline-table;
    }

    .photo-details p {
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

    .percent-level-1 {
        color: #a6e005 !important;
    }

    .percent-level-2 {
        color: #fc8d06 !important;
    }

    .percent-level-3 {
        color: rgb(255, 77, 77) !important;
    }
</style>
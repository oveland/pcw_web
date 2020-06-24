<template>
    <div class="container-photo col-md-12" v-if="photo && photo.details">
        <div v-if="photo.details.persons">
            <div v-for="draw in photo.details.occupation.draws" class="zone-detection" :class="draw.count === true && !draw.profile ? 'undetected' : ''" @mouseenter="setBusySeating(draw)" @mouseleave="clearBusySeating"
                 :title="`${!draw.overlap ? $t('Seat')+': '+draw.profileStr : $t('Overlap')}`"
                 :style="`height: ${draw.box.heightOrig && true ? draw.box.heightOrig : draw.box.height}%; width: ${draw.box.width}%; top: ${draw.box.top}%; left: ${draw.box.left}%; border-color: ${draw.color}; background: ${draw.background};`">
                <small>{{ draw.confidence }}%</small>
            </div>
            <div v-for="draw in photo.details.occupation.draws" class="zone-detection-center"
                 :style="`height: 1px; width: 1px; top: ${draw.box.center ? draw.box.center.top : 0}%; left: ${draw.box.center ? draw.box.center.left : 0}%;border-color: ${draw.color};`">
            </div>
        </div>

        <seat-component v-if="styleSeating.show && seat.width > 0 && seat.height > 0" v-for="seat in seating" :key="seat.id" :seat.sync="seat" :style="`opacity: ${styleSeating ? styleSeating.opacity/100 : 100}`" :image="image" :fixed="fixedSeating"></seat-component>
        <seat-component v-if="busySeat.width > 0 && busySeat.height > 0" v-for="busySeat in busySeating" :key="busySeat.id + Math.random()" :seat="busySeat" :image="image" :fixed="true"></seat-component>
        <seat-component v-if="seatOccupied && seatOccupied.width > 0 && seatOccupied.height > 0" :seat="seatOccupied" :image="image" :fixed="true"></seat-component>
        <img id="image-seating" v-if="photo.details.url" draggable="false" :src="photo.details.url.encoded ? photo.details.url.encoded : photo.details.url" :width="`${image.size.width}px`" :height="`${image.size.height}px`" alt="Seating photo">
    </div>
</template>

<script>
    import SeatComponent from './SeatComponent';

    export default {
        name: "PhotoPersonsComponent",
        components: {
            SeatComponent
        },
        props: {
            photo: Object,
            seating: Array,
            fixedSeating: Boolean,
            styleSeating: Object
        },
        data() {
            return {
                busySeating: [],
                seatOccupied: {},
                image: {
                    size: {
                        width: 1000,
                        height: 700,
                    },
                },
            }
        },
        methods: {
            setBusySeating(draw) {
                if(draw.profile){
                    this.busySeating = draw.profile.seating;
                    this.seatOccupied = draw.profile.seatOccupied;
                }
            },
            clearBusySeating() {
                this.busySeating = [];
            },
        }
    }
</script>

<style scoped>
    .container-photo {
        padding: 0 !important;
        width: 1000px;
        height: auto;
    }

    .container-photo .zone-detection {
        position: absolute;
        /*background: rgba(137, 138, 135, 0.54);*/
        /*color: #9bef00;*/
        border-width: 2px;
        border-style: solid;
        border-radius: 5px;
        z-index: 200;
        padding: 5px;
    }

    .container-photo .zone-detection.undetected {
        border: 3px solid red !important;
        border-radius: 4px;
    }

    .container-photo .zone-detection-center {
        border-style: solid;
        border-width: 2px;
        position: absolute;
        z-index: 100000 !important;
    }

    .container-photo .zone-detection small {
        font-size: 0.5em;
        color: #e2e1e1;
        float: left;
    }
</style>
<template>
    <div class="container-photo col-md-12" v-if="photo && photo.details">
        <div v-if="false && photo.details.occupation">
            <div v-if="draw.box.heightOrig" v-for="draw in photo.details.occupation.draws" class="zone-detection zone-detection-original"
                 :style="`height: ${draw.box.heightOrig}%; width: ${draw.box.width}%; top: ${draw.box.top}%; left: ${draw.box.left}%; border-color: #d3d3d354; background: rgba(255, 255, 255, 0.04);border-width:1px !important`">
            </div>

            <div v-for="draw in photo.details.occupation.draws" class="zone-detection" :class="`${draw.count === true && !draw.profile ? 'undetected' : ''} ${draw.selectedClass}`" @mouseenter="setBusySeating(draw)" @mouseleave="clearBusySeating(draw)"
                 :title="`${!draw.overlap ? $t('Seat')+': ' + draw.profileStr : $t('Overlap')}`"
                 :style="`height: ${draw.box.height}%; width: ${draw.box.width}%; top: ${draw.box.top}%; left: ${draw.box.left}%; border-color: ${draw.color}; background: ${draw.background};`">
                <small>{{ draw.confidence }}%</small>
            </div>

            <div v-for="draw in photo.details.occupation.draws" class="zone-detection-center" :class="draw.selectedClass" :title="`${draw.largeDetection ? 'LDT: ' : 'Normal'}: ${draw.relationSize} | ${draw.overlap ? $t('Overlap') : ''} Count: ${draw.count ? $t('YES') : $t('NO')}`"
                 :style="`height: 1px; width: 1px; top: ${draw.box.center ? draw.box.center.top : 0}%; left: ${draw.box.center ? draw.box.center.left : 0}%;border-color: ${draw.color};`">
            </div>
        </div>

        <seat-component v-if="false && styleSeating.show && seat.width > 0 && seat.height > 0" v-for="seat in seating" :key="seat.id" :seat.sync="seat" :seating-occupied="seatingOccupied" :style="`opacity: ${styleSeating ? styleSeating.opacity/100 : 100}`" :image="image" :fixed="fixedSeating"></seat-component>
        <seat-component v-if="false && busySeat.width > 0 && busySeat.height > 0" v-for="busySeat in busySeating" :key="busySeat.id + Math.random()" :seat="busySeat" :image="image" :fixed="true"></seat-component>
        <seat-component v-if="false && seatOccupied && seatOccupied.width > 0 && seatOccupied.height > 0" :seat="seatOccupied" :seating-occupied="seatingOccupied" :image="image" :fixed="true"></seat-component>
        <img  id="image-seating" v-if="photo.details.url" draggable="false" v-lazy="photo.details.url.encoded ? photo.details.url.encoded : photo.details.url + '&title=1'" :width="`${image.size.width}px`" :height="`${image.size.height}px`" alt="Seating photo">

        <div class="container-actions text-center" v-if="false && photo && seating.length > 0">
            <div class="p-10 actions">
                <div class="md-checkbox has-success">
                    <input type="checkbox" :id="`show-photo-${photo.id}${fixedSeating ? '':'-profile'}`" class="md-check" v-model="styleSeating.show">
                    <label :for="`show-photo-${photo.id}${fixedSeating ? '':'-profile'}`">
                        <span class="inc"></span>
                        <span class="check"></span>
                        <span class="box text-muted"></span>
                    </label>
                    <range-slider
                            class="slider"
                            min="0"
                            max="100"
                            step="1"
                            v-model="styleSeating.opacity">
                    </range-slider>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import RangeSlider from 'vue-range-slider';
    import 'vue-range-slider/dist/vue-range-slider.css';

    import VueLazyload from 'vue-lazyload';
    Vue.use(VueLazyload);

    import SeatComponent from './SeatComponent';

    export default {
        name: "PhotoPersonsComponent",
        components: {
            RangeSlider,
            SeatComponent
        },
        props: {
            photo: Object,
            seating: Array,
            fixedSeating: Boolean,
        },
        watch:{
            styleSeating(){
                this.clearBusySeating();
            }
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
                styleSeating: {
                    show: false,
                    opacity: 30,
                },
            }
        },
        computed: {
            seatingOccupied(){
                return this.photo.details.occupation ? this.photo.details.occupation.seatingOccupied : {};
            }
        },
        methods: {
            setBusySeating(draw) {
                if(draw.profile){
                    draw.selectedClass = 'selected';
                    this.busySeating = draw.profile.seating;
                    this.seatOccupied = draw.profile.seatOccupied;
                }
            },
            clearBusySeating(draw) {
                draw.selectedClass = '';
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
        border-radius: 50%;
        z-index: 100000 !important;
    }

    .container-photo .zone-detection small {
        font-size: 0.5em;
        color: #e2e1e1;
        float: left;
    }

    .container-photo .zone-detection.selected, .container-photo .zone-detection-center.selected {
        border-color: #fc0050 !important;
        border-radius: 2px;
        z-index: 10000 !important;
    }

    .slider{
        margin: 0;
    }

    .container-actions .actions {
        margin: auto;
        width: 20%;
        background: #15181da8;
    }

    .container-actions {
        position: absolute;
        bottom: 5px;
        z-index: 10000;
        /*background: rgba(0, 0, 0, 0.05);*/
        border-radius: 10px;
        margin: auto;
        width: 100%;
    }
</style>
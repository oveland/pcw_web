<template>
    <div v-if="photo" class="zone-seating-component photo-header-report" :style="`width: ${image.size.width + 6}px; margin: auto`">
        <div v-if="true">
            <div class="photo">
                <p>
                <span class="title">
                    <i class="fa fa-users"></i>
                </span>
                    <span class="detail">{{ $t('General count') }}: {{ photo.passengers.total }}</span>
                </p>
            </div>
            <div class="passengers" v-if="photo.passengers">
                <ol>
                    <li v-for="roundTrip in photo.passengers.byRoundTrips" class="detail">
                        <p v-show="roundTrip.number">
                            <small class="text-uppercase">
                                {{ $t('Round trip') }} {{ roundTrip.number }} ({{ roundTrip.from }} - {{ roundTrip.to }}) ➤ {{ roundTrip.route }}: {{ roundTrip.count }}
                            </small>
                        </p>
                    </li>
                </ol>
                <p class="detail count-round-trips">
                    <small>{{ $t('Total') }} {{ photo.passengers.byRoundTrips.length }} {{ $t('round trips') }}, {{ $t('passengers') }}: {{ photo.passengers.total }}</small>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
    import VueJsonPretty from 'vue-json-pretty';
    import PhotoDetailsComponent from './PhotoDetailsComponent';
    import PhotoPersonsComponent from './PhotoPersonsComponent';
    import Swal from 'sweetalert2/dist/sweetalert2.min';

    import RangeSlider from 'vue-range-slider';
    import 'vue-range-slider/dist/vue-range-slider.css';

    export default {
        name: "ProfileSeatingComponent",
        components: {
            PhotoDetailsComponent,
            PhotoPersonsComponent,
            VueJsonPretty,
            RangeSlider
        },
        props: {
            searchParams: Object,
            apiUrl: String,
        },
        watch: {
            searchParams() {
                this.load();
            }
        },
        data() {
            return {
                image: {
                    size: {
                        width: 1000,
                        height: 700,
                    }
                },
                photo: Object,
                seating: []
            }
        },
        computed: {
            selected() {
                return _.find(this.seating, function (seat) {
                    return seat.selected;
                });
            }
        },
        methods: {
            addSeat() {
                this.seating.push({
                    id: Date.now(),
                    number: null,
                    selected: false,
                    top: this.selected ? this.selected.top + 10 : 30,
                    left: this.selected ? this.selected.left + 10 : 30,
                    width: this.selected ? this.selected.width : 10,
                    height: this.selected ? this.selected.height : 10,
                    center: {
                        left: 450 + 100 / 2,
                        top: 300 + 50 / 2
                    }
                });
            },
            deleteSeating() {
                if (this.selected) {
                    this.seating = _.filter(this.seating, (seat) => {
                        return seat.id !== this.selected.id;
                    });
                }
            },
            load() {
				this.seating = [];
				this.photo = null;

                Swal.fire({
                    title: this.$t('Loading'),
                    text: this.$t('Please wait'),
                    target: '.tab-profile-seating',
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    },
                    heightAuto: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false
                });

                axios.get(`${this.apiUrl}/params/occupation/get`, {params: this.searchParams}).then(response => {
                    const data = response.data;

                    if (data.success) {
                        this.seating = data.seating;
                        this.photo = data.photo;
                    } else {
                        gerror(data.message);
                    }
                }).catch((error) => {
                    gerror(this.$t('An error occurred in the process. Contact your administrator') + '!');
                    console.log(error);
                }).then(() => {
                    Swal.close();
                });
            },
            save() {
                Swal.fire({
                    title: this.$t('Processing'),
                    text: this.$t('Please wait'),
                    target: '.tab-profile-seating',
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    },
                    heightAuto: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false
                });
                axios.post(`${this.apiUrl}/params/occupation/save`, {
                    vehicle: this.searchParams.vehicle,
                    seating: this.seating,
                }).then(response => {
                    const data = response.data;

                    if (data.success) {
                        gsuccess(data.message);
                    } else {
                        gerror(data.message);
                    }
                }).catch((error) => {
                    gerror(this.$t('An error occurred in the process. Contact your administrator') + '!');
                    console.log(error);
                }).then(() => {
                    Swal.close();
                });
            },
        },
        created() {
            this.photo = null;
            this.load();
        }
    }
</script>

<style scoped>
    .photo-header-report {
        color: white;
        padding: 20px;
        height: auto !important;
        background: rgb(20, 26, 34);
    }

    .passengers .detail p{
        margin: 0;
    }

    .count-round-trips{
        margin-top: -5px;
        margin-left: 25px;
        border-top: 1px solid #d3d3d378;
        width: 20%;
    }
</style>
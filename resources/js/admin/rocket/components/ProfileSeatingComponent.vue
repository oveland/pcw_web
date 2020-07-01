<template>
    <div class="zone-seating-component" :style="`width: ${image.size.width}px; margin: auto`">
        <div v-if="photo">
            <photo-details-component :photo="photo"></photo-details-component>

            <photo-persons-component :photo="photo" :seating="seating" :style-seating="styleSeating" :fixed-seating="false"></photo-persons-component>

            <div class="container-actions col-md-12">
                <button class="btn btn-default btn-sm btn-action" @click="load">
                    <i class="fa fa-refresh"></i>
                </button>
                <button class="btn btn-info btn-sm btn-action" @click="addSeat">
                    <i class="fa fa-plus-square"></i>
                </button>
                <button class="btn btn-success btn-sm btn-action" @click="save">
                    <i class="fa fa-save"></i>
                </button>
                <button :disabled="!selected" class="btn btn-warning btn-sm btn-action m-l-20" @click="deleteSeating">
                    <i class="fa fa-trash"></i>
                </button>
                <br>

                <div class="md-checkbox has-success">
                    <input type="checkbox" id="checkbox-zone-seating" class="md-check" v-model="styleSeating.show">
                    <label for="checkbox-zone-seating">
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

        <div v-if="!photo" class="col-md-12 text-center p-40">
            <img v-if="true" draggable="false" src="/img/rocket/profile.svg" width="30%">
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
                styleSeating: {
                    show: Boolean,
                    opacity: 100,
                },
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
    .container-actions {
        margin: auto;
        width: 1000px;
        text-align: center;
    }

    .btn-action {
        margin: 5px;
        display: inline;
    }
</style>
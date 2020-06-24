<template>
    <div class="report-photo-component" style="width: 1000px; margin: auto">
        <div class="container-actions text-center" v-if="seating.length > 0">
            <div class="p-10">
                <div class="md-checkbox has-success">
                    <input type="checkbox" id="checkbox14" class="md-check" v-model="styleSeating.show">
                    <label for="checkbox14">
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

        <div v-for="photo in photos">
            <photo-details-component :photo="photo"></photo-details-component>
            <photo-persons-component :photo="photo" :fixed-seating="true" :seating="seating" :style-seating="styleSeating"></photo-persons-component>
        </div>

        <div class="col-md-12 text-center p-40">
            <img v-if="true" draggable="false" src="/img/rocket/report.svg" width="30%">
        </div>
    </div>
</template>

<script>
    import Swal from 'sweetalert2/dist/sweetalert2.min';
    import PhotoDetailsComponent from './PhotoDetailsComponent';
    import PhotoPersonsComponent from './PhotoPersonsComponent';

    import RangeSlider from 'vue-range-slider';
    import 'vue-range-slider/dist/vue-range-slider.css';

    export default {
        name: "ReportPhotoComponent",
        components: {
            Swal,
            PhotoDetailsComponent,
            PhotoPersonsComponent,
            RangeSlider
        },
        props: {
            searchParams: Object,
            apiUrl: String,
        },
        data() {
            return {
                photos: Array,
                seating: [],
                image: {
                    size: {
                        width: 1000,
                        height: 700,
                    }
                },
                styleSeating: {
                    show: false,
                    opacity: 100,
                },
            }
        },
        watch: {
            searchParams() {
                this.load();
            }
        },
        methods: {
            load() {
                Swal.fire({
                    title: this.$t('Loading'),
                    text: this.$t('Please wait'),
                    target: '.tab-report-photos',
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    },
                    heightAuto: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false
                });
                axios.get(`${this.apiUrl}/report/historic`, {params: this.searchParams}).then(response => {
                    const data = response.data;

                    if (data.success) {
                        this.seating = data.seating;
                        this.photos = data.photos;
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
        }
    }
</script>

<style scoped>
    .slider{
        margin: 0;
    }

    .container-actions{
        position: fixed;
        z-index: 10000;
        background: rgba(0, 0, 0, 0.05);
        border-radius: 10px;
        margin-left: 15%;
    }
</style>
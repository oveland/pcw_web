<template>
    <div class="report-photo-component" :style="`width: ${image.size.width + 6}px; margin: auto;overflow-y:auto;height:${image.size.height + 10}px`">
        <div v-for="photo in photos">
            <photo-details-component :photo="photo" style="margin-bottom: 5px"></photo-details-component>
            <photo-persons-component :photo="photo" :fixed-seating="true" :seating="seating"></photo-persons-component>
        </div>

        <div v-if="photos.length <= 0" class="col-md-12 text-center p-40">
            <img v-if="true" draggable="false" src="/img/rocket/report.svg" width="30%">
        </div>
    </div>
</template>

<script>
    import Swal from 'sweetalert2/dist/sweetalert2.min';
    import PhotoDetailsComponent from './PhotoDetailsComponent';
    import PhotoPersonsComponent from './PhotoPersonsComponent';


    export default {
        name: "ReportPhotoComponent",
        components: {
            Swal,
            PhotoDetailsComponent,
            PhotoPersonsComponent,
        },
        props: {
            searchParams: Object,
            apiUrl: String,
        },
        data() {
            return {
                photos: [],
                seating: [],
                image: {
                    size: {
                        width: 1000,
                        height: 700,
                    }
                }
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

</style>
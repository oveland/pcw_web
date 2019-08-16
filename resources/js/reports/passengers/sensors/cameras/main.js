import SearchComponent from './components/SearchComponent';
import PhotosComponent from './components/PhotosComponent';
import PhotoDetail from './components/PhotoDetail';

let camerasReportView = new Vue({
    el: '#cameras-report',
    components: {
        SearchComponent,
        PhotosComponent,
        PhotoDetail
    },
    data: {
        urlList: String,
        photos: [],
        photoDetail: {},
        search: {
            vehicles: [],
            vehicle: {},
            date: String,
        },
    },
    computed: {
        searchParams: function () {
            return {
                date: this.search.date,
                vehicle: this.search.vehicle.id,
            }
        },
    },
    methods: {
        searchReport: function () {
            const form = $('.form-search-report');
            form.find('.btn-search-report').addClass(loadingClass);

            axios.get(this.urlList, {params: this.searchParams}).then((r) => {
                this.photos = r.data;
            }).catch(function (error) {
                console.log(error);
            }).then(function () {
                $('.report-container').hide().fadeIn();
                form.find('.btn-search-report').removeClass(loadingClass);
            });
        },
        setPhotoDetail: function(photo) {
            this.photoDetail = photo;
        }
    },
    mounted: function () {
        this.urlList = this.$el.attributes.url.value;
    },
});
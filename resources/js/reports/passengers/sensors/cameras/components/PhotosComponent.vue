<template>
    <div class="row">
        <div class="col-xs-12 col-md-2 col-sm-3" v-for="photo in photos">
            <div class="thumbnail" data-toggle="modal" data-target="#modal-photo" @click="setPhotoDetail(photo)">
                <img src="/thumbnail.jpeg">
                <div class="caption">
                    <h3><i class="fa fa-camera"></i> {{ photo.date }}</h3>
                </div>
            </div>
        </div>

        <div v-if="!photos.length" class="alert alert-warning alert-bordered m-b-10 mb-10 mt-10 col-md-6 col-md-offset-3 offset-md-3">
            <div class="col-md-2" style="padding-top: 10px">
                <i class="fa fa-3x fa-exclamation-circle"></i>
            </div>
            <div class="col-md-10">
                <span class="close pull-right" data-dismiss="alert">×</span>
                <h4><strong>Ups!</strong></h4>
                <hr class="hr">
                No registers found
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "PhotosComponent",
        props: {
            photos: Array,
            photoDetail: Object,
        },
        data: function () {
            return {
                params: []
            }
        },
        methods: {
            setPhotoDetail: function (photo) {
                this.$emit("detail", photo);
            }
        }
    }
</script>

<style scoped>
    .thumbnail{
        cursor: pointer;
    }
</style>
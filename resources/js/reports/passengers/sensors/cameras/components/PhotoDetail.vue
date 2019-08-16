<template>
    <div class="row">
        <div class="col-md-12" v-if="photo && photo.location">
            <img :src="urlPhoto + '/' + photo.id+'?f='+photo.f" width="100%">
            <hr>
            <h2 class="text-center">Created: {{ photo.date }}</h2>
        </div>
    </div>
</template>

<script>
    export default {
        name: "PhotoDetail",
        props:{
            urlPhoto: String,
            photo:Object
        }
    }
</script>

<style scoped>
    img{
        -webkit-border-radius: 10px;
        -moz-border-radius: 10px;
        border-radius: 10px;
    }
</style>
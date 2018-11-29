<style>
    body{
        margin: 0;
    }
    iframe{
        border: none !important;
        width: 100%;
    }
</style>
<iframe id="container" src="{{ $link }}"></iframe>
<script>
    document.getElementById('container').style.height = (window.innerHeight)+'px';
</script>
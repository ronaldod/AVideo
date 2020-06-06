<?php
$liveLink = "Invalid link";
if (filter_var($t['link'], FILTER_VALIDATE_URL)) {
    $url = parse_url($t['link']);
    if ($url['scheme'] == 'https') {
        $liveLink = $t['link'];
    } else {
        $liveLink = "{$global['webSiteRootURL']}plugin/LiveLinks/proxy.php?livelink=" . urlencode($t['link']);
    }
}
?>
<link href="<?php echo $global['webSiteRootURL']; ?>plugin/Live/view/live.css" rel="stylesheet" type="text/css"/>
<div class="row main-video" id="mvideo">
    <div class="firstC col-sm-2 col-md-2"></div>
    <div class="secC col-sm-8 col-md-8">
        <div id="videoContainer">
            <div id="floatButtons" style="display: none;">
                <p class="btn btn-outline btn-xs move">
                    <i class="fas fa-expand-arrows-alt"></i>
                </p>
                <button type="button" class="btn btn-outline btn-xs" onclick="closeFloatVideo();floatClosed = 1;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="main-video" class="embed-responsive embed-responsive-16by9">
                <video poster="<?php echo $global['webSiteRootURL']; ?>plugin/Live/view/OnAir.jpg" controls 
                       class="embed-responsive-item video-js vjs-default-skin vjs-big-play-centered" 
                       id="mainVideo" data-setup='{ "aspectRatio": "16:9",  "techorder" : ["flash", "html5"] }'>
                    <source src="<?php echo $liveLink; ?>" type='application/x-mpegURL'>
                </video>
            </div>
        </div>
    </div>
    <div class="col-sm-2 col-md-2"></div>
</div>
<script>

    $(document).ready(function () {
        if (typeof player === 'undefined') {
            player = videojs('mainVideo'<?php echo PlayerSkins::getDataSetup(); ?>);
        }
        player.ready(function () {
            var err = this.error();
            if (err && err.code) {
                $('.vjs-error-display').hide();
                $('#mainVideo').find('.vjs-poster').css({'background-image': 'url(<?php echo $global['webSiteRootURL']; ?>plugin/Live/view/Offline.jpg)'});
            }
<?php
if ($config->getAutoplay()) {
    echo "playerPlay(0);";
}
?>

        });
        player.persistvolume({
            namespace: "AVideo"
        });
    });
</script>
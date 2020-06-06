<?php
global $isLive;
$isLive = 1;
require_once '../../videos/configuration.php';
require_once $global['systemRootPath'] . 'objects/user.php';
require_once $global['systemRootPath'] . 'objects/subscribe.php';
require_once $global['systemRootPath'] . 'objects/functions.php';
require_once $global['systemRootPath'] . 'plugin/Live/Objects/LiveTransmition.php';

if (!empty($_GET['c'])) {
    $user = User::getChannelOwner($_GET['c']);
    if (!empty($user)) {
        $_GET['u'] = $user['user'];
    }
}


$livet = LiveTransmition::getFromDbByUserName($_GET['u']);
$uuid = $livet['key'];

$u = new User(0, $_GET['u'], false);
$user_id = $u->getBdId();
$video['users_id'] = $user_id;
$subscribe = Subscribe::getButton($user_id);
$name = $u->getNameIdentificationBd();
$name = "<a href='" . User::getChannelLink($user_id) . "' class='btn btn-xs btn-default'>{$name} " . User::getEmailVerifiedIcon($user_id) . "</a>";
$video['creator'] = '<div class="pull-left"><img src="' . User::getPhoto($user_id) . '" alt="" class="img img-responsive img-circle" style="max-width: 40px;"/></div><div class="commentDetails" style="margin-left:45px;"><div class="commenterName text-muted"><strong>' . $name . '</strong><br>' . $subscribe . '</div></div>';

$img = "{$global['webSiteRootURL']}plugin/Live/getImage.php?u={$_GET['u']}&format=jpg";
$imgw = 640;
$imgh = 360;

$liveDO = AVideoPlugin::getObjectData("Live");
$video['type'] = 'video';
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
    <head>
        <title><?php echo $livet['title']; ?> - <?php echo __("Live Video"); ?> - <?php echo $config->getWebSiteTitle(); ?></title>
        <link href="<?php echo $global['webSiteRootURL']; ?>js/video.js/video-js.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $global['webSiteRootURL']; ?>js/videojs-contrib-ads/videojs.ads.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $global['webSiteRootURL']; ?>css/player.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $global['webSiteRootURL']; ?>js/webui-popover/jquery.webui-popover.min.css" rel="stylesheet" type="text/css"/>
        <?php
        include $global['systemRootPath'] . 'view/include/head.php';
        ?>

        <meta property="fb:app_id"             content="774958212660408" />
        <meta property="og:url"                content="<?php echo $global['webSiteRootURL']; ?>plugin/Live/?u=<?php echo $_GET['u']; ?>" />
        <meta property="og:type"               content="video.other" />
        <meta property="og:title"              content="<?php echo str_replace('"', '', $livet['title']); ?> - <?php echo $config->getWebSiteTitle(); ?>" />
        <meta property="og:description"        content="<?php echo str_replace('"', '', $livet['title']); ?>" />
        <meta property="og:image"              content="<?php echo $img; ?>" />
        <meta property="og:image:width"        content="<?php echo $imgw; ?>" />
        <meta property="og:image:height"       content="<?php echo $imgh; ?>" />
        <?php 
        echo AVideoPlugin::getHeadCode();
        ?>
    </head>

    <body class="<?php echo $global['bodyClass']; ?>">
        <?php
        include $global['systemRootPath'] . 'view/include/navbar.php';
        $lt = new LiveTransmition($livet['id']);
        if ($lt->userCanSeeTransmition()) {
            ?>

            <div class="container-fluid principalContainer">
                <div class="row">
                    <div class="col-md-12">
                        <center style="margin:5px;">
                            <?php echo getAdsLeaderBoardTop(); ?>
                        </center>
                    </div>  
                    <div class="col-md-12">
                        <?php
                        require "{$global['systemRootPath']}plugin/Live/view/liveVideo.php";
                        ?>
                    </div>  
                    <div class="col-md-12">
                        <center style="margin:5px;">
                            <?php echo getAdsLeaderBoardTop2(); ?>
                        </center>
                    </div>  
                </div>
                <div class="row" id="modeYoutubeBottom" style="margin: 0;">

                    <div class="col-sm-1 col-md-1"></div>
                    <div class="col-sm-6 col-md-6" id="modeYoutubeBottomContent">
                        <div class="panel">
                            <div class="panel-body">
                                <h1 itemprop="name">
                                    <i class="fas fa-video"></i> <?php echo $livet['title']; ?>
                                </h1>
                                <div class="col-xs-12 col-sm-12 col-lg-12"><?php echo $video['creator']; ?></div>
                                <p><?php echo nl2br(textToLink($livet['description'])); ?></p>
                                <div class="row">
                                    <div class="col-md-12 watch8-action-buttons text-muted">

                                        <?php echo AVideoPlugin::getWatchActionButton(0); ?>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-lg-12 col-sm-12 col-xs-12 extraVideos nopadding"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-4 bgWhite list-group-item rightBar" id="yptRightBar" style="">

                        <?php
                        echo getAdsSideRectangle();
                        ?>

                    </div>
                    <div class="col-sm-1 col-md-1"></div>
                </div>    

            </div>










            <?php
        } else {
            ?>
            <h1 class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> <?php echo __("You are not allowed see this streaming"); ?></h1>    
            <?php
        }
        ?>

        <script src="<?php echo $global['webSiteRootURL']; ?>js/video.js/video.min.js" type="text/javascript"></script>
        <script src="<?php echo $global['webSiteRootURL']; ?>js/videojs-contrib-ads/videojs.ads.min.js" type="text/javascript"></script>
        <script src="<?php echo $global['webSiteRootURL']; ?>plugin/Live/view/videojs-contrib-hls.min.js" type="text/javascript"></script>
        <?php
        include $global['systemRootPath'] . 'view/include/footer.php';
        ?>  
        <?php
        if (!empty($p)) {
            $p->getChat($uuid);
        }
        ?>
        <script src="<?php echo $global['webSiteRootURL']; ?>js/videojs-persistvolume/videojs.persistvolume.js" type="text/javascript"></script>
        <script src="<?php echo $global['webSiteRootURL']; ?>js/webui-popover/jquery.webui-popover.min.js" type="text/javascript"></script>
        <script src="<?php echo $global['webSiteRootURL']; ?>js/bootstrap-list-filter/bootstrap-list-filter.min.js" type="text/javascript"></script>

    </body>
</html>

<?php
include $global['systemRootPath'] . 'objects/include_end.php';
?>

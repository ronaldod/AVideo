<?php
global $global, $config;
if (!isset($global['systemRootPath'])) {
    require_once '../videos/configuration.php';
}
require_once $global['systemRootPath'] . 'objects/user.php';
require_once $global['systemRootPath'] . 'objects/functions.php';
require_once $global['systemRootPath'] . 'plugin/Gallery/functions.php';
require_once $global['systemRootPath'] . 'objects/subscribe.php';

$siteTitle = $config->getWebSiteTitle();

$obj = AVideoPlugin::getObjectData("Gallery");
if (!empty($_GET['type'])) {
    if ($_GET['type'] == 'audio') {
        $_SESSION['type'] = 'audio';
    } else if ($_GET['type'] == 'video') {
        $_SESSION['type'] = 'video';
    } else {
        unset($_SESSION['type']);
    }
}
require_once $global['systemRootPath'] . 'objects/category.php';
$currentCat;
$currentCatType;
if (!empty($_GET['catName'])) {
    $currentCat = Category::getCategoryByName($_GET['catName']);
    $currentCatType = Category::getCategoryType($currentCat['id']);
    $siteTitle = "{$currentCat['name']}";
}
if ((empty($_GET['type'])) && (!empty($currentCatType))) {
    if ($currentCatType['type'] == "1") {
        $_SESSION['type'] = "audio";
    } else if ($currentCatType['type'] == "2") {
        $_SESSION['type'] = "video";
    } else {
        unset($_SESSION['type']);
    }
}
require_once $global['systemRootPath'] . 'objects/video.php';
$orderString = "";
if ($obj->sortReverseable) {
    if (strpos($_SERVER['REQUEST_URI'], "?") != false) {
        $orderString = $_SERVER['REQUEST_URI'] . "&";
    } else {
        $orderString = $_SERVER['REQUEST_URI'] . "/?";
    }
    $orderString = str_replace("&&", "&", $orderString);
    $orderString = str_replace("//", "/", $orderString);
}
$video = Video::getVideo("", "viewable", !$obj->hidePrivateVideos, false, true);
if (empty($video)) {
    $video = Video::getVideo("", "viewable", !$obj->hidePrivateVideos, true);
}
if (empty($_GET['page'])) {
    $_GET['page'] = 1;
} else {
    $_GET['page'] = intval($_GET['page']);
}
$total = 0;
$totalPages = 0;
$url = '';
$args = '';
if (strpos($_SERVER['REQUEST_URI'], "?") != false) {
    $args = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "?"), strlen($_SERVER['REQUEST_URI']));
}
if (strpos($_SERVER['REQUEST_URI'], "/cat/") === false) {
    $url = $global['webSiteRootURL'] . "page/";
} else {
    $url = $global['webSiteRootURL'] . "cat/" . $video['clean_category'] . "/page/";
}
$contentSearchFound = false;
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
    <head>
        <title><?php
            echo $siteTitle;
            ?></title>
        <?php include $global['systemRootPath'] . 'view/include/head.php'; ?>
        <script src="<?php echo $global['webSiteRootURL']; ?>view/js/infinite-scroll.pkgd.min.js" type="text/javascript"></script>
    </head>

    <body class="<?php echo $global['bodyClass']; ?>">
        <?php include $global['systemRootPath'] . 'view/include/navbar.php'; ?>
        <div class="container-fluid gallery">
            <div class="row text-center" style="padding: 10px;">
                <?php echo getAdsLeaderBoardTop(); ?>
            </div>
            <div class="col-sm-10 col-sm-offset-1 list-group-item">

                <div class="row mainArea">
                    <?php
                    if (!empty($currentCat)) {
                        include $global['systemRootPath'] . 'plugin/Gallery/view/Category.php';
                    }

                    if ($obj->searchOnChannels && !empty($_GET['search'])) {
                        $channels = User::getAllUsers(true);
                        clearSearch();
                        foreach ($channels as $value) {
                            $contentSearchFound = true;
                            createChannelItem($value['id'], $value['photoURL'], $value['identification']);
                        }
                        reloadSearch();
                    }

                    if (!empty($video)) {
                        $contentSearchFound = true;
                        $img_portrait = ($video['rotation'] === "90" || $video['rotation'] === "270") ? "img-portrait" : "";
                        if (empty($_GET['search'])) {
                            include $global['systemRootPath'] . 'plugin/Gallery/view/BigVideo.php';
                        }
                        ?>
                        <center style="margin:5px;">
                            <?php echo getAdsLeaderBoardTop2(); ?>
                        </center>
                        <!-- For Live Videos -->
                        <div id="liveVideos" class="clear clearfix" style="display: none;">
                            <h3 class="galleryTitle text-danger"> <i class="fas fa-play-circle"></i> <?php echo __("Live"); ?></h3>
                            <div class="row extraVideos"></div>
                        </div>
                        <script>
                            function afterExtraVideos($liveLi) {
                                $liveLi.removeClass('col-lg-12 col-sm-12 col-xs-12 bottom-border');
                                $liveLi.find('.thumbsImage').removeClass('col-lg-5 col-sm-5 col-xs-5');
                                $liveLi.find('.videosDetails').removeClass('col-lg-7 col-sm-7 col-xs-7');
                                $liveLi.addClass('col-lg-2 col-md-4 col-sm-4 col-xs-6 fixPadding');
                                $('#liveVideos').slideDown();
                                return $liveLi;
                            }
                        </script>
                        <?php
                        echo AVideoPlugin::getGallerySection();
                        ?>
                        <!-- For Live Videos End -->
                        <?php
                        if ($obj->Suggested) {
                            createGallery(!empty($obj->SuggestedCustomTitle) ? $obj->SuggestedCustomTitle : __("Suggested"), 'suggested', $obj->SuggestedRowCount, 'SuggestedOrder', "", "", $orderString, "ASC", !$obj->hidePrivateVideos, "fas fa-star");
                        }
                        if ($obj->Trending) {
                            createGallery(!empty($obj->TrendingCustomTitle) ? $obj->TrendingCustomTitle : __("Trending"), 'trending', $obj->TrendingRowCount, 'TrendingOrder', "zyx", "abc", $orderString, "ASC", !$obj->hidePrivateVideos, "fas fa-chart-line");
                        }
                        if ($obj->SortByName) {
                            createGallery(!empty($obj->SortByNameCustomTitle) ? $obj->SortByNameCustomTitle : __("Sort by name"), 'title', $obj->SortByNameRowCount, 'sortByNameOrder', "zyx", "abc", $orderString, "ASC", !$obj->hidePrivateVideos, "fas fa-font");
                        }
                        if ($obj->DateAdded) {
                            createGallery(!empty($obj->DateAddedCustomTitle) ? $obj->DateAddedCustomTitle : __("Date added"), 'created', $obj->DateAddedRowCount, 'dateAddedOrder', __("newest"), __("oldest"), $orderString, "DESC", !$obj->hidePrivateVideos, "far fa-calendar-alt");
                        }
                        if ($obj->MostWatched) {
                            createGallery(!empty($obj->MostWatchedCustomTitle) ? $obj->MostWatchedCustomTitle : __("Most watched"), 'views_count', $obj->MostWatchedRowCount, 'mostWatchedOrder', __("Most"), __("Fewest"), $orderString, "DESC", !$obj->hidePrivateVideos, "far fa-eye");
                        }
                        if ($obj->MostPopular) {
                            createGallery(!empty($obj->MostPopularCustomTitle) ? $obj->MostPopularCustomTitle : __("Most popular"), 'likes', $obj->MostPopularRowCount, 'mostPopularOrder', __("Most"), __("Fewest"), $orderString, "DESC", !$obj->hidePrivateVideos, "fas fa-fire");
                        }
                        if ($obj->SubscribedChannels && User::isLogged() && empty($_GET['showOnly'])) {
                            $channels = Subscribe::getSubscribedChannels(User::getId());
                            foreach ($channels as $value) {
                                $_POST['disableAddTo'] = 0;
                                createChannelItem($value['users_id'], $value['photoURL'], $value['identification'], $obj->SubscribedChannelsRowCount);
                            }
                        }
                        if ($obj->Categories && empty($_GET['catName']) && empty($_GET['showOnly'])) {
                            ?>
                            <div id="categoriesContainer"></div>
                            <p class="pagination infiniteScrollPagination">
                                <a class="pagination__next" href="<?php echo $global['webSiteRootURL']; ?>plugin/Gallery/view/modeGalleryCategory.php?current=1"></a>
                            </p>
                            <div class="scroller-status">
                                <div class="infinite-scroll-request loader-ellips text-center">
                                    <i class="fas fa-spinner fa-pulse text-muted"></i>
                                </div>
                            </div>
                            <script>
                                $(document).ready(function () {
                                    $container = $('#categoriesContainer').infiniteScroll({
                                        path: '.pagination__next',
                                        append: '.categoriesContainerItem',
                                        status: '.scroller-status',
                                        hideNav: '.infiniteScrollPagination',
                                        prefill: true,
                                        history: false
                                    });
                                    $container.on('request.infiniteScroll', function (event, path) {
                                        console.log('Loading page: ' + path);
                                    });
                                    $container.on('append.infiniteScroll', function (event, response, path, items) {
                                        console.log('Append page: ' + path);
                                        lazyImage();
                                    });
                                });

                                function lazyImage() {
                                    $('.thumbsJPG').lazy({
                                        effect: 'fadeIn',
                                        visibleOnly: true,
                                        // called after an element was successfully handled
                                        afterLoad: function (element) {
                                            element.removeClass('blur');
                                            element.parent().find('.thumbsGIF').lazy({
                                                effect: 'fadeIn'
                                            });
                                        }
                                    });
                                    mouseEffect();
                                }
                            </script>
                            <?php
                        }
                        ?>

                        <?php
                    } else {
                        echo AVideoPlugin::getGallerySection();
                        $contentSearchFound = true;
                    }

                    if (!$contentSearchFound) {
                        ?>
                        <div class="alert alert-warning">
                            <span class="glyphicon glyphicon-facetime-video"></span>
                            <strong><?php echo __("Warning"); ?>!</strong>
                            <?php echo __("We have not found any videos or audios to show"); ?>.
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div id="TrailerModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="<?php echo $global['webSiteRootURL']; ?>plugin/Gallery/script.js" type="text/javascript"></script>
        <?php include $global['systemRootPath'] . 'view/include/footer.php'; ?>
        <script>
                $('#TrailerModal').modal({show: false});
                function showTrailer(iframe) {
                    $('#TrailerModal iframe').attr('src', iframe);
                    $('#TrailerModal').modal("show");
                    return false;
                }
                $('#TrailerModal').on('hidden.bs.modal', function () {
                    $('#TrailerModal iframe').attr('src', '');
                });
        </script>

    </body>
</html>
<?php include $global['systemRootPath'] . 'objects/include_end.php'; ?>

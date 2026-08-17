<?php
if (!defined('_GNUBOARD_')) exit;
include_once G5_THEME_PATH.'/skin/board/video/_lib.php';

$list_count = (is_array($list) && $list) ? count($list) : 0;
$sid = 'moidam-swiper-video';
?>
<div class="swiper moidam-swiper moidam-swiper-video moidam_video_latest" id="<?php echo $sid ?>">
    <div class="swiper-wrapper">
<?php if ($list_count === 0) { ?>
        <div class="swiper-slide">
            <p class="pl_gal_empty">등록된 동영상이 없습니다.</p>
        </div>
<?php } ?>
<?php for ($i = 0; $i < $list_count; $i++) {
    $item = $list[$i];
    $href = get_pretty_url($item['bo_table'], $item['wr_id']);
    $row = moidam_video_row($item['bo_table'], $item['wr_id']);
    $img_src = moidam_video_thumb_src($row['thumb']);
    $dur = moidam_video_duration($row['duration']);
    $ready = !empty($row['ready']);
?>
        <div class="swiper-slide">
            <a href="<?php echo $href; ?>" class="pl_gal_link">
                <span class="pl_gal_thumb moidam_vl_thumb">
                    <?php if ($img_src) { ?>
                    <img src="<?php echo htmlspecialchars($img_src, ENT_QUOTES); ?>" alt="" loading="lazy">
                    <?php } else { ?>
                    <span class="moidam_vl_empty"><i class="fa fa-play-circle" aria-hidden="true"></i></span>
                    <?php } ?>
                    <span class="moidam_vl_play" aria-hidden="true"><i class="fa fa-play"></i></span>
                    <?php if (!$ready && $row['uid']) { ?>
                    <span class="moidam_vl_proc">처리중</span>
                    <?php } elseif ($dur) { ?>
                    <span class="moidam_vl_dur"><?php echo $dur; ?></span>
                    <?php } ?>
                    <?php if ($item['icon_new']) { ?><span class="pl_gal_new">N</span><?php } ?>
                </span>
                <span class="pl_gal_tit"><?php echo $item['subject']; ?><?php echo function_exists('moidam_cmt_badge') ? moidam_cmt_badge($item['wr_comment'] ?? $item['comment_cnt'] ?? 0) : ''; ?></span>
            </a>
        </div>
<?php } ?>
    </div>
    <div class="swiper-pagination"></div>
</div>

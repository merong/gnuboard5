<?php if (!defined('_GNUBOARD_')) exit;
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

$thumb_width  = 320;
$thumb_height = 214;
$list_count   = (is_array($list) && $list) ? count($list) : 0;
$sid = 'moidam-swiper-'.preg_replace('/[^a-z0-9_]/i', '', (string) $bo_table);
?>
<div class="swiper moidam-swiper" id="<?php echo $sid ?>">
    <div class="swiper-wrapper">
<?php if ($list_count === 0) { ?>
        <div class="swiper-slide">
            <p class="pl_gal_empty">등록된 이미지가 없습니다.</p>
        </div>
<?php } ?>
<?php for ($i = 0; $i < $list_count; $i++) {
    $item  = $list[$i];
    $href  = get_pretty_url($item['bo_table'], $item['wr_id']);
    $thumb = get_list_thumbnail($item['bo_table'], $item['wr_id'], $thumb_width, $thumb_height, false, true);
    $img_src = $thumb['src'] ?: G5_IMG_URL.'/no_img.png';
    $img_alt = $thumb['alt'] ?: $item['subject'];
?>
        <div class="swiper-slide">
            <a href="<?php echo $href; ?>" class="pl_gal_link">
                <span class="pl_gal_thumb">
                    <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($img_alt, ENT_QUOTES); ?>" loading="lazy">
                    <?php if ($item['icon_new']) { ?><span class="pl_gal_new">N</span><?php } ?>
                </span>
                <span class="pl_gal_tit"><?php echo $item['subject']; ?><?php echo function_exists('moidam_cmt_badge') ? moidam_cmt_badge($item['wr_comment'] ?? $item['comment_cnt'] ?? 0) : ''; ?></span>
            </a>
        </div>
<?php } ?>
    </div>
    <div class="swiper-pagination"></div>
</div>

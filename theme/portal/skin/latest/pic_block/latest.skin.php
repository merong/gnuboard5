<?php if (!defined('_GNUBOARD_')) exit;
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

$thumb_width  = 168;
$thumb_height = 112;
$list_count   = (is_array($list) && $list) ? count($list) : 0;
?>
<ul class="pl_gallery">
<?php if ($list_count === 0) { ?>
    <li class="pl_gal_empty">등록된 이미지가 없습니다.</li>
<?php } ?>
<?php for ($i = 0; $i < $list_count; $i++) {
    $item  = $list[$i];
    $href  = get_pretty_url($item['bo_table'], $item['wr_id']);
    $thumb = get_list_thumbnail($item['bo_table'], $item['wr_id'], $thumb_width, $thumb_height, false, true);
    $img_src = $thumb['src'] ?: G5_IMG_URL.'/no_img.png';
    $img_alt = $thumb['alt'] ?: $item['subject'];
?>
    <li class="pl_gal_item">
        <a href="<?php echo $href; ?>" class="pl_gal_link">
            <span class="pl_gal_thumb">
                <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($img_alt, ENT_QUOTES); ?>"
                     width="<?php echo $thumb_width; ?>" height="<?php echo $thumb_height; ?>">
                <?php if ($item['icon_new']) { ?>
                    <span class="pl_gal_new">N</span>
                <?php } ?>
            </span>
            <span class="pl_gal_tit"><?php echo $item['subject']; ?><?php echo moidam_cmt_badge($item['wr_comment'] ?? $item['comment_cnt'] ?? 0); ?></span>
        </a>
    </li>
<?php } ?>
</ul>

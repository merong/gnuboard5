<?php
if (!defined('_GNUBOARD_')) exit;
echo '<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">'.PHP_EOL;
$list_count = (is_array($list) && $list) ? count($list) : 0;
?>
<div class="ptl_lat">
    <div class="ptl_lat_head">
        <a href="<?php echo get_pretty_url($bo_table) ?>" class="ptl_lat_title"><?php echo $bo_subject ?></a>
        <a href="<?php echo get_pretty_url($bo_table) ?>" class="ptl_lat_more">더보기 ›</a>
    </div>
    <ul class="ptl_lat_list">
        <?php if ($list_count === 0): ?>
        <li class="ptl_lat_empty">등록된 게시물이 없습니다.</li>
        <?php endif; ?>
        <?php for ($i = 0; $i < $list_count; $i++):
            $item = $list[$i];
            $href = get_pretty_url($bo_table, $item['wr_id']);
        ?>
        <li class="ptl_lat_item<?php echo $item['is_notice'] ? ' ptl_lat_notice' : '' ?>">
            <a href="<?php echo $href ?>" class="ptl_lat_link">
                <?php if ($item['is_notice']): ?><em class="ptl_lat_badge_notice">공지</em><?php endif; ?>
                <?php if ($item['icon_secret']): ?><i class="fa fa-lock ptl_lat_lock"></i><?php endif; ?>
                <span class="ptl_lat_tit"><?php echo $item['subject'] ?></span>
                <?php echo moidam_cmt_badge($item['wr_comment'] ?? $item['comment_cnt'] ?? 0); ?>
                <?php if ($item['icon_new'] && !$item['is_notice']): ?><span class="ptl_lat_new">N</span><?php endif; ?>
            </a>
            <span class="ptl_lat_date"><?php echo $item['datetime2'] ?></span>
        </li>
        <?php endfor; ?>
    </ul>
</div>

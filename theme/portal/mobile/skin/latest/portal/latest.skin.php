<?php
if (!defined('_GNUBOARD_')) exit;
echo '<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">'.PHP_EOL;
$list_count = (is_array($list) && $list) ? count($list) : 0;
?>
<div class="mptl_lat">
    <div class="mptl_lat_head">
        <a href="<?php echo get_pretty_url($bo_table) ?>" class="mptl_lat_title"><?php echo $bo_subject ?></a>
        <a href="<?php echo get_pretty_url($bo_table) ?>" class="mptl_lat_more">더보기 ›</a>
    </div>
    <ul class="mptl_lat_list">
        <?php if ($list_count === 0): ?>
        <li class="mptl_lat_empty">등록된 게시물이 없습니다.</li>
        <?php endif; ?>
        <?php for ($i = 0; $i < $list_count; $i++):
            $item = $list[$i];
            $href = get_pretty_url($bo_table, $item['wr_id']);
        ?>
        <li class="mptl_lat_item<?php echo $item['is_notice'] ? ' mptl_lat_notice' : '' ?>">
            <a href="<?php echo $href ?>" class="mptl_lat_link">
                <?php if ($item['is_notice']): ?><em class="mptl_lat_badge_notice">공지</em><?php endif; ?>
                <?php if ($item['icon_secret']): ?><i class="fa fa-lock mptl_lat_lock"></i><?php endif; ?>
                <span class="mptl_lat_tit"><?php echo $item['subject'] ?></span>
                <?php if ($item['icon_new'] && !$item['is_notice']): ?><span class="mptl_lat_new">N</span><?php endif; ?>
                <?php echo moidam_cmt_badge($item['wr_comment'] ?? $item['comment_cnt'] ?? 0); ?>
            </a>
            <span class="mptl_lat_date"><?php echo $item['datetime2'] ?></span>
        </li>
        <?php endfor; ?>
    </ul>
</div>

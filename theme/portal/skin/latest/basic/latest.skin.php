<?php if (!defined('_GNUBOARD_')) exit;

$list_count = (is_array($list) && $list) ? count($list) : 0;
?>
<ul class="pl_list">
<?php if ($list_count === 0) { ?>
    <li class="pl_empty">등록된 게시물이 없습니다.</li>
<?php } ?>
<?php for ($i = 0; $i < $list_count; $i++) {
    $item = $list[$i];
    $href = get_pretty_url($item['bo_table'], $item['wr_id']);
    $nick = get_text($item['wr_name'] ?? strip_tags($item['name'] ?? ''));
?>
    <li class="pl_item<?php echo $item['is_notice'] ? ' pl_notice' : ''; ?>">
        <a href="<?php echo $href; ?>" class="pl_link">
            <?php if ($item['is_notice']) { ?>
                <em class="pl_badge pl_badge_notice">공지</em>
            <?php } ?>
            <?php if ($item['icon_secret']) { ?>
                <i class="fa fa-lock pl_icon_lock" aria-hidden="true"></i>
            <?php } ?>
            <span class="pl_tit"><?php echo $item['subject']; ?></span>
            <?php echo moidam_cmt_badge($item['wr_comment'] ?? $item['comment_cnt'] ?? 0); ?>
            <?php if ($item['icon_new'] && !$item['is_notice']) { ?>
                <span class="pl_new_dot"></span>
            <?php } ?>
        </a>
        <?php if ($nick) { ?>
            <span class="pl_nick"><?php echo $nick; ?></span>
        <?php } ?>
        <span class="pl_date"><?php echo $item['datetime2']; ?></span>
    </li>
<?php } ?>
</ul>
<a href="<?php echo get_pretty_url($bo_table); ?>" class="pl_more">더보기 &rsaquo;</a>

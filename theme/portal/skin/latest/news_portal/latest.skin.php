<?php if (!defined('_GNUBOARD_')) exit;

$list_count = (is_array($list) && $list) ? count($list) : 0;
?>
<ul class="nv_news_list">
<?php if ($list_count == 0) { ?>
    <li class="nv_news_empty">등록된 게시물이 없습니다.</li>
<?php } ?>
<?php for ($i = 0; $i < $list_count; $i++) {
    $item   = $list[$i];
    $href   = get_pretty_url($item['bo_table'], $item['wr_id']);
    $is_new = $item['icon_new'];
    $is_hot = $item['icon_hot'];
?>
<li class="nv_news_item<?php echo $item['is_notice'] ? ' is_notice' : ''; ?>">
    <a href="<?php echo $href; ?>" class="nv_news_link">
        <span class="nv_news_tit">
            <?php if ($item['is_notice']) { ?>
                <em class="nv_badge badge_notice">공지</em>
            <?php } elseif ($is_hot) { ?>
                <em class="nv_badge badge_hot">인기</em>
            <?php } ?>
            <?php echo $item['subject']; ?>
            <?php echo moidam_cmt_badge($item['wr_comment'] ?? $item['comment_cnt'] ?? 0); ?>
            <?php if ($is_new && !$item['is_notice']) { ?>
                <span class="nv_new_dot" title="새글"></span>
            <?php } ?>
        </span>
    </a>
    <span class="nv_news_meta">
        <span class="nv_source"><?php echo $item['name']; ?></span>
        <span class="nv_dot_sep">·</span>
        <span class="nv_time"><?php echo $item['datetime2']; ?></span>
    </span>
</li>
<?php } ?>
</ul>
<a href="<?php echo get_pretty_url($bo_table); ?>" class="nv_more_btn">더보기 &rsaquo;</a>

<?php if (!defined('_GNUBOARD_')) exit; ?>
<ul class="sl_list">
<?php if (empty($list)): ?>
    <li class="sl_empty">등록된 게시물이 없습니다.</li>
<?php else: foreach ($list as $item): ?>
    <li class="sl_item<?php echo $item['is_notice'] ? ' sl_notice' : ''; ?>">
        <a href="<?php echo get_pretty_url($item['bo_table'], $item['wr_id']); ?>" class="sl_link">
            <?php if ($item['is_notice']): ?>
                <em class="sl_badge">공지</em>
            <?php endif; ?>
            <span class="sl_tit"><?php echo $item['subject']; ?></span>
            <?php echo moidam_cmt_badge($item['wr_comment'] ?? $item['comment_cnt'] ?? 0); ?>
            <?php if ($item['icon_new'] && !$item['is_notice']): ?>
                <span class="sl_new">N</span>
            <?php endif; ?>
        </a>
        <span class="sl_date"><?php echo $item['datetime2']; ?></span>
    </li>
<?php endforeach; endif; ?>
</ul>

<?php
if (!defined('_GNUBOARD_')) exit;
if (!in_array($bo_table, ['free', 'humor'], true)) return;

$wt = $g5['write_prefix'] . $bo_table;
$ranges = [
    'today' => ['label' => '오늘 인기글', 'where' => "wr_datetime >= CURDATE()"],
    'week'  => ['label' => '주간 인기글', 'where' => "wr_datetime >= DATE_SUB(NOW(), INTERVAL 7 DAY)"],
    'month' => ['label' => '월간 인기글', 'where' => "wr_datetime >= DATE_SUB(NOW(), INTERVAL 30 DAY)"],
];
$pop = [];
foreach ($ranges as $key => $meta) {
    $sql = "SELECT wr_id, wr_subject, wr_hit, wr_comment, wr_datetime, wr_name
            FROM {$wt}
            WHERE wr_is_comment = 0 AND {$meta['where']}
            ORDER BY wr_hit DESC, wr_datetime DESC
            LIMIT 5";
    $rs = sql_query($sql, false);
    $rows = [];
    if ($rs) {
        while ($row = sql_fetch_array($rs)) $rows[] = $row;
    }
    $pop[$key] = $rows;
}
$uid = 'pop_'.$bo_table;
?>
<div class="moidam_pop" data-pop="<?php echo $uid ?>">
    <div class="moidam_pop_nav" role="tablist">
        <?php $i=0; foreach ($ranges as $key => $meta): ?>
        <button type="button" class="moidam_pop_tab<?php echo $key==='month'?' is-on':'' ?>" data-pop-tab="<?php echo $key ?>"><?php echo $meta['label'] ?></button>
        <?php $i++; endforeach; ?>
    </div>
    <div class="moidam_pop_swipe">
    <div class="moidam_pop_track">
    <?php $i=0; foreach ($ranges as $key => $meta): ?>
    <ol class="moidam_pop_list<?php echo $key==='month'?' is-on':'' ?>" data-pop-panel="<?php echo $key ?>">
        <?php if (!$pop[$key]): ?>
        <li class="moidam_pop_empty">아직 인기글이 없어요.</li>
        <?php else: foreach ($pop[$key] as $pi => $pr):
            $href = get_pretty_url($bo_table, $pr['wr_id']);
        ?>
        <li>
            <span class="moidam_pop_rank"><?php echo $pi+1 ?></span>
            <a href="<?php echo $href ?>" class="moidam_pop_tit"><?php echo get_text(cut_str($pr['wr_subject'], 42, '…')) ?></a>
            <?php echo moidam_cmt_badge($pr['wr_comment'] ?? 0); ?>
            <span class="moidam_pop_hit"><i class="fa fa-eye"></i> <?php echo number_format((int)$pr['wr_hit']) ?></span>
        </li>
        <?php endforeach; endif; ?>
    </ol>
    <?php $i++; endforeach; ?>
    </div>
    </div>
</div>
<script>
jQuery(function($){
    $(document).on('click', '.moidam_pop_tab', function(){
        var $w = $(this).closest('.moidam_pop');
        var key = $(this).data('pop-tab');
        $w.find('.moidam_pop_tab').removeClass('is-on');
        $(this).addClass('is-on');
        $w.find('.moidam_pop_list').removeClass('is-on');
        $w.find('.moidam_pop_list[data-pop-panel="'+key+'"]').addClass('is-on');
    });
});
</script>

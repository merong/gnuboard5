<?php
if (!defined('_GNUBOARD_')) exit;
if (empty($list) || !is_array($list)) return;
$action = G5_BBS_URL.'/search.php';
?>
<div class="sch_recent_box">
    <p class="sch_recent_label">최근 검색어</p>
    <ul class="sch_recent_list">
    <?php foreach ($list as $row):
        $word = get_text($row['pp_word']);
        if ($word === '') continue;
        $href = $action.'?sfl=wr_subject||wr_content&amp;sop=and&amp;stx='.urlencode($row['pp_word']);
    ?>
        <li><a href="<?php echo $href ?>"><?php echo $word ?></a></li>
    <?php endforeach; ?>
    </ul>
</div>

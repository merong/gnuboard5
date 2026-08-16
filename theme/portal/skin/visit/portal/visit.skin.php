<?php
if (!defined('_GNUBOARD_')) exit;
global $is_admin;
/* CSS는 default.css의 visit_* 클래스를 공유 */
?>
<ul class="visit_list">
    <li class="visit_item">
        <span class="visit_label">오늘</span>
        <span class="visit_val"><?php echo number_format($visit[1]) ?></span>
    </li>
    <li class="visit_item">
        <span class="visit_label">어제</span>
        <span class="visit_val"><?php echo number_format($visit[2]) ?></span>
    </li>
    <li class="visit_item">
        <span class="visit_label">최대</span>
        <span class="visit_val"><?php echo number_format($visit[3]) ?></span>
    </li>
    <li class="visit_item">
        <span class="visit_label">전체</span>
        <span class="visit_val visit_total"><?php echo number_format($visit[4]) ?></span>
    </li>
</ul>
<?php if ($is_admin == 'super'): ?>
<a href="<?php echo G5_ADMIN_URL ?>/visit_list.php" class="visit_admin_link"><i class="fa fa-bar-chart"></i> 방문자 통계</a>
<?php endif; ?>

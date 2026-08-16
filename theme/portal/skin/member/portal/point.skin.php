<?php if (!defined('_GNUBOARD_')) exit;
echo '<link rel="stylesheet" href="'.G5_THEME_URL.'/skin/member/portal/style.css">'.PHP_EOL;
?>
<div class="pw_wrap">

    <!-- 헤더 -->
    <div class="pw_header">
        <h1 class="pw_title"><i class="fa fa-star-o"></i> 포인트 내역</h1>
        <button type="button" class="pw_close_x" onclick="window.close()">×</button>
    </div>

    <!-- 보유 포인트 카드 -->
    <div class="pw_summary">
        <div class="pw_summary_inner">
            <span class="pw_summary_label">보유 포인트</span>
            <strong class="pw_summary_value"><?php echo number_format($member['mb_point']) ?><em>P</em></strong>
        </div>
        <div class="pw_summary_icon"><i class="fa fa-trophy"></i></div>
    </div>

    <!-- 내역 목록 -->
    <ul class="pw_list">
    <?php
    $sum_p = $sum_m = 0;
    foreach ((array)$list as $row) {
        $is_plus = ($row['po_point'] > 0);
        if ($is_plus) {
            $sum_p   += $row['po_point'];
            $num_str  = '+' . number_format($row['po_point']);
            $cls      = 'pw_plus';
        } else {
            $sum_m   += $row['po_point'];
            $num_str  = number_format($row['po_point']);
            $cls      = 'pw_minus';
        }
        $expire = '';
        if ($row['po_expired'] == 1) {
            $expire = '만료 ' . substr(str_replace('-', '', $row['po_expire_date']), 2);
        } elseif ($row['po_expire_date'] && $row['po_expire_date'] != '9999-12-31') {
            $expire = $row['po_expire_date'];
        }
    ?>
    <li class="pw_item">
        <div class="pw_dot <?php echo $cls ?>"></div>
        <div class="pw_info">
            <span class="pw_content"><?php echo $row['po_content'] ?></span>
            <span class="pw_date"><?php echo $row['po_datetime'] ?>
                <?php if ($expire) { ?> · <span class="pw_expire"><?php echo $expire ?></span><?php } ?>
            </span>
        </div>
        <span class="pw_point <?php echo $cls ?>"><?php echo $num_str ?>P</span>
    </li>
    <?php } ?>
    <?php if (empty($list)) { ?>
    <li class="pw_empty"><i class="fa fa-star-o"></i><br>포인트 내역이 없습니다.</li>
    <?php } ?>
    </ul>

    <!-- 페이징 + 닫기 -->
    <div class="pw_footer">
        <?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page=') ?>
        <button type="button" class="pw_close_btn" onclick="window.close()">닫기</button>
    </div>

</div>

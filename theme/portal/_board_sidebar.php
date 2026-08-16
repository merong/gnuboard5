<?php
/*
 * 게시판 사이드바 파셜
 * head.php / tail.php 에서 board.php 컨텍스트에서만 include 됨
 * 사용 가능 변수: $board, $bo_table, $g5, $member, $is_admin, $portal_settings
 */
if (!defined('_GNUBOARD_')) exit;

global $board, $bo_table, $g5, $member, $is_admin, $is_member, $portal_settings;

$mb_level = isset($member['mb_level']) ? (int)$member['mb_level'] : 1;
$cur_gr   = $board['gr_id'] ?? '';

/* ── 같은 그룹의 게시판 목록 ────────────────────────────── */
$gr_boards = [];
if ($cur_gr) {
    $sql = "SELECT bo_table, bo_subject
            FROM {$g5['board_table']}
            WHERE gr_id = '" . addslashes($cur_gr) . "'
              AND bo_list_level <= {$mb_level}
              AND bo_device <> 'mobile'";
    if (!$is_admin) $sql .= " AND bo_use_cert = ''";
    $sql .= " ORDER BY bo_order";
    $br = sql_query($sql, false);
    while ($b = sql_fetch_array($br)) {
        $gr_boards[] = $b;
    }
}

/* ── 같은 그룹 최신글 ───────────────────────────────────── */
$gr_latest = [];
if ($gr_boards) {
    $unions = [];
    foreach ($gr_boards as $gb) {
        $wt = $g5['write_prefix'] . addslashes($gb['bo_table']);
        $bt = addslashes($gb['bo_table']);
        $unions[] = "SELECT '{$bt}' AS bo_table, wr_id, wr_subject, wr_datetime
                     FROM {$wt} WHERE wr_is_comment = 0";
    }
    $sql = "SELECT * FROM (" . implode(' UNION ALL ', $unions) . ") AS _u
            ORDER BY wr_datetime DESC LIMIT 8";
    $lr = sql_query($sql, false);
    while ($row = sql_fetch_array($lr)) {
        $gr_latest[] = $row;
    }
}

/* ── 같은 그룹 인기글 (최근 30일, 조회수순) ────────────── */
$gr_popular = [];
if ($gr_boards) {
    $unions = [];
    foreach ($gr_boards as $gb) {
        $wt = $g5['write_prefix'] . addslashes($gb['bo_table']);
        $bt = addslashes($gb['bo_table']);
        $unions[] = "SELECT '{$bt}' AS bo_table, wr_id, wr_subject, wr_hit, wr_datetime
                     FROM {$wt}
                     WHERE wr_is_comment = 0
                       AND wr_datetime >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    }
    $sql = "SELECT * FROM (" . implode(' UNION ALL ', $unions) . ") AS _u
            ORDER BY wr_hit DESC, wr_datetime DESC LIMIT 8";
    $pr = sql_query($sql, false);
    while ($row = sql_fetch_array($pr)) {
        $gr_popular[] = $row;
    }
}
?>
<aside class="portal_board_side">

    <div class="widget_box widget_login">
        <?php echo outlogin('theme/basic'); ?>
    </div>

    <!-- 애드센스 광고 -->
    <?php if (!empty($portal_settings['board_sidebar_ad']) && portal_ad_allowed()): ?>
    <div class="brd_side_ad">
        <?php echo $portal_settings['board_sidebar_ad']; ?>
    </div>
    <?php endif; ?>

    <!-- 같은 그룹 게시판 목록 -->
    <?php if ($gr_boards): ?>
    <div class="brd_side_widget">
        <div class="brd_side_whead">
            <span class="brd_side_wtitle">같은 그룹 게시판</span>
        </div>
        <ul class="brd_side_board_list">
            <?php foreach ($gr_boards as $gb):
                $is_cur = ($gb['bo_table'] === $bo_table);
                $b_href = get_pretty_url($gb['bo_table']);
            ?>
            <li class="brd_side_board_item<?php echo $is_cur ? ' brd_side_board_cur' : '' ?>">
                <a href="<?php echo $b_href ?>">
                    <?php if ($is_cur): ?><i class="fa fa-angle-right" aria-hidden="true"></i><?php endif; ?>
                    <?php echo get_text($gb['bo_subject']) ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- 같은 그룹 최신글 -->
    <?php if ($gr_latest): ?>
    <div class="brd_side_widget">
        <div class="brd_side_whead">
            <span class="brd_side_wtitle">그룹 최신글</span>
        </div>
        <ul class="brd_side_post_list">
            <?php foreach ($gr_latest as $lt):
                $lt_href = get_pretty_url($lt['bo_table'], $lt['wr_id']);
                $lt_subj = get_text(cut_str($lt['wr_subject'], 28));
                $lt_date = substr($lt['wr_datetime'], 0, 10);
            ?>
            <li class="brd_side_post_item">
                <a href="<?php echo $lt_href ?>" class="brd_side_post_link" title="<?php echo htmlspecialchars(get_text($lt['wr_subject']), ENT_QUOTES) ?>">
                    <?php echo $lt_subj ?>
                </a>
                <span class="brd_side_post_date"><?php echo $lt_date ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- 같은 그룹 인기글 -->
    <?php if ($gr_popular): ?>
    <div class="brd_side_widget">
        <div class="brd_side_whead">
            <span class="brd_side_wtitle">그룹 인기글</span>
            <span class="brd_side_wbadge">30일</span>
        </div>
        <ul class="brd_side_post_list">
            <?php foreach ($gr_popular as $idx => $pt):
                $pt_href = get_pretty_url($pt['bo_table'], $pt['wr_id']);
                $pt_subj = get_text(cut_str($pt['wr_subject'], 26));
            ?>
            <li class="brd_side_post_item">
                <span class="brd_side_rank<?php echo $idx < 3 ? ' brd_side_rank_top' : '' ?>"><?php echo $idx + 1 ?></span>
                <a href="<?php echo $pt_href ?>" class="brd_side_post_link" title="<?php echo htmlspecialchars(get_text($pt['wr_subject']), ENT_QUOTES) ?>">
                    <?php echo $pt_subj ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

</aside><!-- /.portal_board_side -->

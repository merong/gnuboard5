<?php
if (!defined('_GNUBOARD_')) exit;

/* bbs/group.php 가 위임한 그룹 페이지 */
global $group, $gr_id, $g5, $member, $is_member, $is_admin, $config;

// 모바일 전용 그룹 접근 제한
if (!$is_admin && isset($group['gr_device']) && $group['gr_device'] === 'mobile') {
    alert($group['gr_subject'] . ' 그룹은 모바일에서만 접근할 수 있습니다.');
}

$g5['title'] = isset($group['gr_subject']) ? $group['gr_subject'] : '';

include_once(G5_THEME_PATH . '/head.php');          // portal_settings 도 여기서 로드됨
include_once(G5_LIB_PATH   . '/latest.lib.php');

/* ── 그룹 내 게시판 목록 조회 ─────────────────────────────── */
$mb_level = isset($member['mb_level']) ? (int)$member['mb_level'] : 1;
$sql = "SELECT bo_table, bo_subject, bo_order
        FROM {$g5['board_table']}
        WHERE gr_id = '" . addslashes($gr_id) . "'
          AND bo_list_level <= {$mb_level}
          AND bo_device <> 'mobile'";
if (!$is_admin) $sql .= " AND bo_use_cert = ''";
$sql .= " ORDER BY bo_order";
$board_result = sql_query($sql);
$group_boards = [];
while ($brow = sql_fetch_array($board_result)) {
    $group_boards[] = $brow;
}

/* ── 사이드바용 변수 (portal cf 필드에서 가져옴) ─────────────── */
$notice_bo  = trim($config['cf_7']) ?: 'notice';
$side_bo    = trim($config['cf_8']) ?: 'free';

$_bo_label = function($bo_table) {
    $b = get_board_db($bo_table, true);
    return ($b && $b['bo_table']) ? get_text($b['bo_subject']) : $bo_table;
};
$notice_label = $_bo_label($notice_bo);
$side_label   = $_bo_label($side_bo);
?>

<div class="portal_main_grid">

    <!-- 왼쪽: 그룹 게시판 목록 -->
    <div class="grid_left">

        <!-- 그룹 헤더 -->
        <div class="group_page_header">
            <nav class="group_breadcrumb" aria-label="breadcrumb">
                <a href="<?php echo G5_URL; ?>">홈</a>
                <span class="bc_sep">&rsaquo;</span>
                <span class="bc_current"><?php echo get_text($group['gr_subject']); ?></span>
            </nav>
            <h2 class="group_page_title"><?php echo get_text($group['gr_subject']); ?></h2>
        </div>

        <?php if (empty($group_boards)): ?>
        <div class="group_empty">
            <p>이 그룹에 접근 가능한 게시판이 없습니다.</p>
            <?php if ($is_admin): ?>
            <a href="<?php echo G5_ADMIN_URL; ?>/board_list.php">게시판 관리</a>
            <?php endif; ?>
        </div>
        <?php else: ?>

        <div class="group_board_grid">
        <?php foreach ($group_boards as $bi => $brow): ?>
            <?php
            $brd     = get_board_db($brow['bo_table'], true);
            $b_label = $brd ? get_text($brd['bo_subject']) : $brow['bo_subject'];
            ?>
            <section class="group_board_section">
                <div class="group_board_head">
                    <h3 class="group_board_name">
                        <a href="<?php echo G5_BBS_URL; ?>/board.php?bo_table=<?php echo $brow['bo_table']; ?>">
                            <?php echo $b_label; ?>
                        </a>
                    </h3>
                    <a href="<?php echo G5_BBS_URL; ?>/board.php?bo_table=<?php echo $brow['bo_table']; ?>" class="group_board_more">더보기 &rsaquo;</a>
                </div>
                <div class="group_board_body">
                    <?php echo latest('theme/basic', $brow['bo_table'], 6, 28); ?>
                </div>
            </section>
        <?php endforeach; ?>
        </div>

        <?php endif; ?>
    </div><!-- /.grid_left -->

    <!-- 오른쪽: 공통 사이드바 -->
    <div class="grid_right">
        <?php include_once(G5_THEME_PATH . '/_sidebar.php'); ?>
    </div>

</div><!-- /.portal_main_grid -->

<?php include_once(G5_THEME_PATH . '/tail.php'); ?>

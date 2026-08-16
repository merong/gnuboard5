<?php if (!defined('_GNUBOARD_')) exit; ?>

<div class="ol_wrap ol_after">
    <div class="ol_profile">
        <span class="ol_avatar"><?php echo get_member_profile_img($member['mb_id']); ?></span>
        <div class="ol_greeting">
            <strong class="ol_nick"><?php echo $nick; ?></strong>님 환영합니다.
            <?php if ($is_admin == 'super' || $is_auth) { ?>
                <a href="<?php echo correct_goto_url(G5_ADMIN_URL); ?>" class="ol_admin_link">관리자</a>
            <?php } ?>
        </div>
    </div>

    <ul class="ol_stats">
        <li>
            <a href="<?php echo G5_BBS_URL ?>/point.php" target="_blank" class="win_point">
                <span class="ol_stat_label">포인트</span>
                <strong class="ol_stat_val"><?php echo $point; ?></strong>
            </a>
        </li>
        <li>
            <a href="<?php echo G5_BBS_URL ?>/memo.php" target="_blank" class="win_memo">
                <span class="ol_stat_label">쪽지</span>
                <strong class="ol_stat_val<?php echo $memo_not_read ? ' ol_stat_new' : ''; ?>"><?php echo $memo_not_read ?: 0; ?></strong>
            </a>
        </li>
        <li>
            <a href="<?php echo G5_BBS_URL ?>/scrap.php" target="_blank" class="win_scrap">
                <span class="ol_stat_label">스크랩</span>
                <strong class="ol_stat_val"><?php echo $mb_scrap_cnt ?: 0; ?></strong>
            </a>
        </li>
        <?php if (function_exists('_atc_render_floating_banner')) {
            $atc_today_done = sql_fetch("SELECT at_id FROM {$g5['table_prefix']}attendance_log WHERE mb_id = '" . addslashes($member['mb_id']) . "' AND at_date = '" . G5_TIME_YMD . "'");
        ?>
        <li>
            <a href="<?php echo G5_PLUGIN_URL ?>/attendance/" target="_blank" class="win_attendance">
                <span class="ol_stat_label">출석</span>
                <strong class="ol_stat_val<?php echo $atc_today_done ? ' ol_stat_done' : ''; ?>"><?php echo $atc_today_done ? '완료' : '미출석'; ?></strong>
            </a>
        </li>
        <?php } ?>
    </ul>

    <div class="ol_actions">
        <a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=register_form.php" class="ol_action_link">정보수정</a>
        <a href="<?php echo G5_BBS_URL ?>/new.php" class="ol_action_link">내 게시물</a>
        <a href="<?php echo G5_BBS_URL ?>/logout.php" class="ol_action_link ol_logout">로그아웃</a>
    </div>
</div>

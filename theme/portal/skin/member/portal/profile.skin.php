<?php
if (!defined('_GNUBOARD_')) exit;
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css?v='.filemtime(__DIR__.'/style.css').'">', 0);

// 프로필 이미지 태그 생성
$profile_img_tag = get_member_profile_img($mb['mb_id']);
$has_img = (strpos($profile_img_tag, '<img') !== false);
?>
<style>
html, body { margin: 0; padding: 0; background: #fff; }
/* 팝업 페이지: popup_win 외 body 직계 요소(탑배너, 로그인메시지 등) 숨김 */
body > *:not(.popup_win) { display: none !important; }
</style>

<div class="popup_win" id="profile">

    <!-- 헤더 -->
    <div class="popup_header">
        <span class="popup_header_icon"><i class="fa fa-user-circle-o"></i></span>
        <h1 class="popup_header_title">프로필</h1>
        <button type="button" class="popup_close_x" onclick="window.close()" title="창 닫기">&#215;</button>
    </div>

    <!-- 프로필 히어로 -->
    <div class="pp_hero">
        <?php if ($has_img): ?>
        <span class="pp_avatar_wrap"><?php echo $profile_img_tag ?></span>
        <?php else: ?>
        <span class="pp_avatar_wrap"><i class="fa fa-user"></i></span>
        <?php endif; ?>
        <div class="pp_nick"><?php echo $mb_nick ?></div>
    </div>

    <!-- 스탯 그리드 -->
    <div class="pp_stats">
        <div class="pp_stat">
            <span class="pp_stat_label"><i class="fa fa-star-o"></i> 권한</span>
            <span class="pp_stat_value pp_em"><?php echo $mb['mb_level'] ?>등급</span>
        </div>
        <div class="pp_stat">
            <span class="pp_stat_label"><i class="fa fa-database"></i> 포인트</span>
            <span class="pp_stat_value pp_em"><?php echo number_format($mb['mb_point']) ?>P</span>
        </div>
        <div class="pp_stat">
            <span class="pp_stat_label"><i class="fa fa-calendar-o"></i> 가입일</span>
            <span class="pp_stat_value">
                <?php echo ($member['mb_level'] >= $mb['mb_level'])
                    ? substr($mb['mb_datetime'], 0, 10).'<br><small style="color:#aaa;font-weight:500">'.number_format($mb_reg_after).'일째</small>'
                    : '비공개'; ?>
            </span>
        </div>
        <div class="pp_stat">
            <span class="pp_stat_label"><i class="fa fa-clock-o"></i> 최종접속</span>
            <span class="pp_stat_value">
                <?php echo ($member['mb_level'] >= $mb['mb_level'])
                    ? substr($mb['mb_today_login'], 0, 10)
                    : '비공개'; ?>
            </span>
        </div>
    </div>

    <?php if ($mb_homepage): ?>
    <a href="<?php echo $mb_homepage ?>" target="_blank" rel="noopener" class="pp_homepage">
        <i class="fa fa-home"></i>
        <span><?php echo $mb_homepage ?></span>
    </a>
    <?php endif; ?>

    <!-- 자기소개 -->
    <div class="pp_intro_section">
        <div class="pp_intro_label"><i class="fa fa-quote-left"></i> 자기소개</div>
        <p class="pp_intro_text"><?php echo $mb_profile ?></p>
    </div>

    <div class="pp_spacer"></div>

    <!-- 하단 버튼 -->
    <div class="popup_footer">
        <button type="button" class="popup_btn_cancel" onclick="window.close()">
            <i class="fa fa-times"></i> 닫기
        </button>
    </div>

</div>

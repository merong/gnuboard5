<?php
if (!defined('_GNUBOARD_')) exit;
add_stylesheet('<link rel="stylesheet" href="'.G5_THEME_URL.'/skin/member/portal/style.css">', 0);
?>

<!-- 로그인 시작 { -->
<div class="pms_wrap">
    <div class="pms_card">

        <div class="pms_logo">
            <a href="<?php echo G5_URL ?>">
                <img src="<?php echo G5_THEME_URL ?>/img/moidam-logo.svg" alt="모담" class="pms_logo_img">
            </a>
        </div>

        <h1 class="pms_title">로그인</h1>

        <form name="flogin" action="<?php echo $login_action_url ?>" onsubmit="return flogin_submit(this);" method="post" autocomplete="off">
        <input type="hidden" name="url" value="<?php echo $login_url ?>">

        <div class="pms_field">
            <label for="login_id" class="pms_label">아이디</label>
            <input type="text" name="mb_id" id="login_id" required class="pms_input" maxlength="20" placeholder="아이디를 입력하세요" autofocus>
        </div>
        <div class="pms_field">
            <label for="login_pw" class="pms_label">비밀번호</label>
            <input type="password" name="mb_password" id="login_pw" required class="pms_input" maxlength="20" placeholder="비밀번호를 입력하세요">
        </div>

        <div class="pms_login_opts">
            <label class="pms_chk_label chk_box">
                <input type="checkbox" name="auto_login" id="login_auto_login" class="selec_chk">
                <span></span> 자동로그인
            </label>
            <a href="<?php echo G5_BBS_URL ?>/password_lost.php" class="pms_link_sm">아이디/비밀번호 찾기</a>
        </div>

        <button type="submit" class="pms_btn_submit">로그인</button>

        </form>

        <?php @include_once(get_social_skin_path().'/social_login.skin.php'); ?>

        <div class="pms_divider"><span>또는</span></div>

        <div class="pms_register_cta">
            <span>아직 회원이 아니신가요?</span>
            <a href="<?php echo G5_BBS_URL ?>/register.php" class="pms_btn_register">회원가입</a>
        </div>

    </div>
</div>

<script>
jQuery(function($) {
    $("#login_auto_login").click(function() {
        if (this.checked) {
            this.checked = confirm("자동로그인을 사용하시면 다음부터 아이디와 비밀번호를 입력하실 필요가 없습니다.\n\n공공장소에서는 개인정보가 유출될 수 있으니 사용을 자제하여 주십시오.\n\n자동로그인을 사용하시겠습니까?");
        }
    });
});
function flogin_submit(f) {
    if ($(document.body).triggerHandler('login_sumit', [f, 'flogin']) !== false) return true;
    return false;
}
</script>
<!-- } 로그인 끝 -->

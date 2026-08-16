<?php
if (!defined('_GNUBOARD_')) exit;
?>

<!-- 로그인 전 아웃로그인 -->
<div class="ol_wrap">
    <form name="foutlogin" action="<?php echo $outlogin_action_url ?>" onsubmit="return fhead_submit(this);" method="post" autocomplete="off">
        <input type="hidden" name="url" value="<?php echo $outlogin_url ?>">

        <div class="ol_inputs">
            <label for="ol_id" class="sound_only">아이디<strong>필수</strong></label>
            <input type="text" id="ol_id" name="mb_id" required maxlength="20" class="ol_input" placeholder="아이디">
            <label for="ol_pw" class="sound_only">비밀번호<strong>필수</strong></label>
            <input type="password" name="mb_password" id="ol_pw" required maxlength="20" class="ol_input" placeholder="비밀번호">
        </div>

        <button type="submit" id="ol_submit" class="ol_btn_login">로그인</button>

        <div class="ol_foot ol_foot_row">
            <label class="ol_auto" for="auto_login">
                <input type="checkbox" name="auto_login" value="1" id="auto_login">
                <span class="ol_chk_box"></span>
                자동로그인
            </label>
            <div class="ol_links">
                <a href="<?php echo G5_BBS_URL ?>/password_lost.php">아이디·비밀번호 찾기</a>
            </div>
        </div>

        <a href="<?php echo G5_BBS_URL ?>/register.php" class="ol_btn_join">회원가입</a>

        <?php
        @include_once(get_social_skin_path().'/social_login.skin.php');
        ?>
    </form>
</div>

<script>
jQuery(function($) {
    $("#auto_login").click(function(){
        if ($(this).is(":checked")) {
            if (!confirm("자동로그인을 사용하시면 다음부터 회원아이디와 비밀번호를 입력하실 필요가 없습니다.\n\n공공장소에서는 개인정보가 유출될 수 있으니 사용을 자제하여 주십시오.\n\n자동로그인을 사용하시겠습니까?"))
                return false;
        }
    });
});

function fhead_submit(f) {
    if ($(document.body).triggerHandler('outlogin1', [f, 'foutlogin']) !== false) {
        return true;
    }
    return false;
}
</script>

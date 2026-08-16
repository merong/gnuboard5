<?php
if (!defined('_GNUBOARD_')) exit;
echo '<link rel="stylesheet" href="'.G5_THEME_URL.'/skin/member/portal/style.css">';
$mb_id = isset($_POST['mb_id']) ? get_text($_POST['mb_id']) : '';
?>

<div class="pms_wrap">
<div class="pms_card">

    <div class="pms_logo">
        <a href="<?php echo G5_URL ?>">
            <span class="pms_logo_text"><?php echo htmlspecialchars($config['cf_title']) ?></span>
        </a>
    </div>

    <h1 class="pms_title">비밀번호 재설정</h1>

    <div class="pfl_id_badge">
        <i class="fa fa-user-circle"></i>
        <span><?php echo htmlspecialchars($mb_id) ?></span>
    </div>

    <p class="pfl_desc">새로운 비밀번호를 입력해 주세요.</p>

    <form name="fpasswordreset" action="<?php echo $action_url ?>" onsubmit="return fpasswordreset_submit(this);" method="post" autocomplete="off">
        <input type="hidden" name="mb_id" value="<?php echo htmlspecialchars($mb_id) ?>">

        <div class="pms_fields">
            <div class="pms_field">
                <label for="mb_pw" class="pms_label">새 비밀번호 <span class="pms_req">*</span></label>
                <input type="password" name="mb_password" id="mb_pw" required class="pms_input" placeholder="새 비밀번호를 입력하세요" autofocus>
            </div>
            <div class="pms_field">
                <label for="mb_pw2" class="pms_label">새 비밀번호 확인 <span class="pms_req">*</span></label>
                <input type="password" name="mb_password_re" id="mb_pw2" required class="pms_input" placeholder="비밀번호를 한 번 더 입력하세요">
                <span class="pms_msg" id="pfl_pw_msg"></span>
            </div>
        </div>

        <div class="pms_actions">
            <button type="submit" class="pms_btn_submit" id="pfl_reset_btn">비밀번호 변경</button>
            <a href="<?php echo G5_BBS_URL ?>/login.php" class="pms_btn_cancel">로그인으로 돌아가기</a>
        </div>
    </form>

</div>
</div>

<script>
jQuery(function($) {
    var $pw  = $("#mb_pw");
    var $pw2 = $("#mb_pw2");
    var $msg = $("#pfl_pw_msg");
    var $btn = $("#pfl_reset_btn");

    function checkMatch() {
        if ($pw2.val() === "") { $msg.text("").css("color", ""); return; }
        if ($pw.val() === $pw2.val()) {
            $msg.text("비밀번호가 일치합니다.").css("color", "var(--pms-primary)");
        } else {
            $msg.text("비밀번호가 일치하지 않습니다.").css("color", "var(--pms-danger)");
        }
    }
    $pw.on("input", checkMatch);
    $pw2.on("input", checkMatch);
});

function fpasswordreset_submit(f) {
    if ($("#mb_pw").val() !== $("#mb_pw2").val()) {
        alert("새 비밀번호와 비밀번호 확인이 일치하지 않습니다.");
        $("#mb_pw2").focus();
        return false;
    }
    if ($("#mb_pw").val().length < 6) {
        alert("비밀번호는 6자리 이상 입력해 주세요.");
        $("#mb_pw").focus();
        return false;
    }
    return true;
}
</script>

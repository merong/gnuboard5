<?php
if (!defined('_GNUBOARD_')) exit;
add_stylesheet('<link rel="stylesheet" href="'.G5_THEME_URL.'/skin/member/portal/style.css">', 0);
?>

<div class="pms_wrap">
<div class="pms_card">

    <div class="pms_logo">
        <a href="<?php echo G5_URL ?>">
            <span class="pms_logo_text"><?php echo htmlspecialchars($g5['title']) ?></span>
        </a>
    </div>

    <h1 class="pms_title">본인 확인</h1>

    <p style="text-align:center; font-size:14px; color:#666; margin:-12px 0 24px;">
        <?php if ($url == 'member_leave.php'): ?>
        비밀번호를 입력하시면 회원탈퇴가 완료됩니다.
        <?php else: ?>
        회원 정보를 안전하게 보호하기 위해<br>비밀번호를 한 번 더 확인합니다.
        <?php endif; ?>
    </p>

    <form name="fmemberconfirm" action="<?php echo $url ?>" onsubmit="return fmemberconfirm_submit(this);" method="post">
    <input type="hidden" name="mb_id" value="<?php echo $member['mb_id'] ?>">
    <input type="hidden" name="w"     value="u">
    <div class="pms_fields">
        <div class="pms_field">
            <label class="pms_label">아이디</label>
            <input type="text" class="pms_input" value="<?php echo htmlspecialchars($member['mb_id']) ?>" readonly>
        </div>
        <div class="pms_field">
            <label for="confirm_mb_password" class="pms_label">비밀번호 <span class="pms_req">*</span></label>
            <input type="password" name="mb_password" id="confirm_mb_password" required
                   class="pms_input frm_input required" maxlength="20" placeholder="비밀번호 입력">
        </div>
    </div>
    <div class="pms_actions" style="margin-top:24px;">
        <button type="submit" id="btn_submit" class="pms_btn_submit">확인</button>
    </div>
    </form>

</div>
</div>

<script>
function fmemberconfirm_submit(f) {
    document.getElementById("btn_submit").disabled = true;
    return true;
}
</script>

<?php
if (!defined('_GNUBOARD_')) exit;
add_stylesheet('<link rel="stylesheet" href="'.G5_THEME_URL.'/skin/member/portal/style.css?v='.filemtime(G5_THEME_PATH.'/skin/member/portal/style.css').'">', 0);
?>

<!-- 회원가입 약관 동의 시작 { -->
<div class="pms_wrap pms_wrap_wide">
    <div class="pms_card pms_card_wide">

        <div class="pms_logo">
            <a href="<?php echo G5_URL ?>">
                <img src="<?php echo G5_THEME_URL ?>/img/moidam-logo.svg" alt="모담" class="pms_logo_img">
            </a>
        </div>

        <h1 class="pms_title">회원가입</h1>

        <p class="pms_agree_notice">
            <i class="fa fa-check-circle" aria-hidden="true"></i>
            회원가입약관 및 개인정보 수집 및 이용의 내용에 동의하셔야 회원가입 하실 수 있습니다.
        </p>

        <?php @include_once(get_social_skin_path().'/social_register.skin.php'); ?>

        <form name="fregister" id="fregister" action="<?php echo $register_action_url ?>" onsubmit="return fregister_submit(this);" method="POST" autocomplete="off">

            <!-- 회원가입 약관 -->
            <div class="pms_agree_section" id="fregister_term">
                <div class="pms_agree_head">
                    <h2 class="pms_agree_title">(필수) 회원가입약관</h2>
                    <label class="pms_chk_label pms_agree_chk" for="agree11">
                        <input type="checkbox" name="agree" value="1" id="agree11">
                        동의합니다
                    </label>
                </div>
                <textarea class="pms_textarea pms_agree_textarea" readonly><?php echo get_text($config['cf_stipulation']) ?></textarea>
            </div>

            <!-- 개인정보 수집 및 이용 -->
            <div class="pms_agree_section" id="fregister_private">
                <div class="pms_agree_head">
                    <h2 class="pms_agree_title">(필수) 개인정보 수집 및 이용</h2>
                    <label class="pms_chk_label pms_agree_chk" for="agree21">
                        <input type="checkbox" name="agree2" value="1" id="agree21">
                        동의합니다
                    </label>
                </div>
                <div class="pms_agree_table_wrap">
                    <table class="pms_agree_table">
                        <caption>개인정보 수집 및 이용</caption>
                        <colgroup>
                            <col style="width:22%">
                            <col style="width:56%">
                            <col style="width:22%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>목적</th>
                            <th>항목</th>
                            <th>보유기간</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>이용자 식별 및 본인여부 확인</td>
                            <td>아이디, 이름, 비밀번호<?php echo ($config['cf_cert_use'])? ", 생년월일, 휴대폰 번호(본인인증 할 때만, 아이핀 제외), 암호화된 개인식별부호(CI)" : ""; ?></td>
                            <td>회원 탈퇴 시까지</td>
                        </tr>
                        <tr>
                            <td>고객서비스 이용에 관한 통지,<br>CS대응을 위한 이용자 식별</td>
                            <td>연락처 (이메일, 휴대전화번호)</td>
                            <td>회원 탈퇴 시까지</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 전체 동의 -->
            <div class="pms_agree_chkall" id="fregister_chkall">
                <label class="pms_chk_label" for="chk_all">
                    <input type="checkbox" name="chk_all" id="chk_all">
                    회원가입 약관에 모두 동의합니다
                </label>
            </div>

            <div class="pms_actions">
                <button type="submit" class="pms_btn_submit">다음 단계</button>
                <a href="<?php echo G5_URL ?>" class="pms_btn_cancel">취소</a>
            </div>

        </form>

    </div>
</div>

<script>
function fregister_submit(f) {
    if (!f.agree.checked) {
        alert("회원가입약관의 내용에 동의하셔야 회원가입 하실 수 있습니다.");
        f.agree.focus();
        return false;
    }
    if (!f.agree2.checked) {
        alert("개인정보 수집 및 이용의 내용에 동의하셔야 회원가입 하실 수 있습니다.");
        f.agree2.focus();
        return false;
    }
    return true;
}

jQuery(function($) {
    $("input[name=chk_all]").click(function() {
        $("input[name^=agree]").prop('checked', this.checked);
    });
    $("input[name=agree], input[name=agree2]").change(function() {
        var allChecked = $("input[name=agree]").prop('checked') && $("input[name=agree2]").prop('checked');
        $("input[name=chk_all]").prop('checked', allChecked);
    });
});
</script>
<!-- } 회원가입 약관 동의 끝 -->

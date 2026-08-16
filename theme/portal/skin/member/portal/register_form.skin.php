<?php
if (!defined('_GNUBOARD_')) exit;

add_stylesheet('<link rel="stylesheet" href="'.G5_THEME_URL.'/skin/member/portal/style.css">', 0);
add_javascript('<script src="'.G5_JS_URL.'/jquery.register_form.js"></script>', 0);
if ($config['cf_cert_use'] && ($config['cf_cert_simple'] || $config['cf_cert_ipin'] || $config['cf_cert_hp']))
    add_javascript('<script src="'.G5_JS_URL.'/certify.js?v='.G5_JS_VER.'"></script>', 0);

$desc_name  = '';
$desc_phone = '';
if ($config['cf_cert_use']) {
    $desc_name  = '<span class="pms_cert_desc">본인확인 시 자동입력</span>';
    $desc_phone = '<span class="pms_cert_desc">본인확인 시 자동입력</span>';
    if (!$config['cf_cert_simple'] && !$config['cf_cert_hp'] && $config['cf_cert_ipin'])
        $desc_phone = '';
}
?>

<!-- 회원가입 시작 { -->
<div class="pms_wrap pms_wrap_wide">
<form id="fregisterform" name="fregisterform" action="<?php echo $register_action_url ?>" onsubmit="return fregisterform_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
<input type="hidden" name="w"         value="<?php echo $w ?>">
<input type="hidden" name="url"       value="<?php echo $urlencode ?>">
<input type="hidden" name="agree"     value="<?php echo $agree ?>">
<input type="hidden" name="agree2"    value="<?php echo $agree2 ?>">
<input type="hidden" name="cert_type" value="<?php echo $member['mb_certify'] ?>">
<input type="hidden" name="cert_no"   value="">
<?php if (isset($member['mb_sex'])): ?><input type="hidden" name="mb_sex" value="<?php echo $member['mb_sex'] ?>"><?php endif; ?>
<?php if (isset($member['mb_nick_date']) && $member['mb_nick_date'] > date("Y-m-d", G5_SERVER_TIME - ($config['cf_nick_modify'] * 86400))): ?>
<input type="hidden" name="mb_nick_default" value="<?php echo get_text($member['mb_nick']) ?>">
<input type="hidden" name="mb_nick"         value="<?php echo get_text($member['mb_nick']) ?>">
<?php endif; ?>

<div class="pms_card pms_card_wide">

    <div class="pms_logo">
        <a href="<?php echo G5_URL ?>">
            <img src="<?php echo G5_THEME_URL ?>/img/moidam-logo.svg" alt="모담" class="pms_logo_img">
        </a>
    </div>

    <h1 class="pms_title"><?php echo ($w == 'u') ? '회원정보 수정' : '회원가입' ?></h1>

    <!-- ── 본인인증 ── -->
    <?php if ($config['cf_cert_use']): ?>
    <section class="pms_section">
        <h2 class="pms_section_title">본인인증</h2>
        <div class="pms_cert_btns">
            <?php if ($config['cf_cert_simple']): ?>
            <button type="button" id="win_sa_kakao_cert" class="pms_btn_cert win_sa_cert" data-type="">간편인증</button>
            <?php endif; ?>
            <?php if ($config['cf_cert_hp']): ?>
            <button type="button" id="win_hp_cert" class="pms_btn_cert">휴대폰 본인확인</button>
            <?php endif; ?>
            <?php if ($config['cf_cert_ipin']): ?>
            <button type="button" id="win_ipin_cert" class="pms_btn_cert">아이핀 본인확인</button>
            <?php endif; ?>
            <span class="pms_req_badge">필수</span>
        </div>
        <?php if ($member['mb_certify']): ?>
        <div id="msg_certify" class="pms_cert_done">
            <?php
            $cert_labels = ['simple'=>'간편인증','ipin'=>'아이핀','hp'=>'휴대폰'];
            echo ($cert_labels[$member['mb_certify']] ?? '') . ' 본인확인';
            if ($member['mb_adult']) echo ' 및 성인인증';
            echo ' 완료';
            ?>
        </div>
        <?php endif; ?>
    </section>
    <?php endif; ?>

    <!-- ── 계정 정보 ── -->
    <section class="pms_section">
        <h2 class="pms_section_title">계정 정보</h2>
        <div class="pms_fields">

            <div class="pms_field">
                <label for="reg_mb_id" class="pms_label">
                    아이디 <span class="pms_req">*</span>
                    <span class="pms_tip">영문자, 숫자, _ 만 입력 가능 (최소 3자)</span>
                </label>
                <input type="text" name="mb_id" value="<?php echo $member['mb_id'] ?>"
                       id="reg_mb_id" <?php echo $required ?> <?php echo $readonly ?>
                       class="pms_input frm_input required <?php echo $readonly ?>"
                       minlength="3" maxlength="20" placeholder="아이디">
                <span id="msg_mb_id" class="pms_msg"></span>
            </div>

            <div class="pms_grid2">
                <div class="pms_field">
                    <label for="reg_mb_password" class="pms_label">비밀번호 <span class="pms_req">*</span></label>
                    <input type="password" name="mb_password" id="reg_mb_password"
                           <?php echo $required ?> class="pms_input frm_input required"
                           minlength="3" maxlength="20" placeholder="비밀번호">
                </div>
                <div class="pms_field">
                    <label for="reg_mb_password_re" class="pms_label">비밀번호 확인 <span class="pms_req">*</span></label>
                    <input type="password" name="mb_password_re" id="reg_mb_password_re"
                           <?php echo $required ?> class="pms_input frm_input required"
                           minlength="3" maxlength="20" placeholder="비밀번호 확인">
                </div>
            </div>

        </div>
    </section>

    <!-- ── 개인 정보 ── -->
    <section class="pms_section">
        <h2 class="pms_section_title">개인 정보</h2>
        <div class="pms_fields">

            <div class="pms_grid2">
                <div class="pms_field">
                    <label for="reg_mb_name" class="pms_label">이름 <span class="pms_req">*</span><?php echo $desc_name ?></label>
                    <input type="text" id="reg_mb_name" name="mb_name"
                           value="<?php echo get_text($member['mb_name']) ?>"
                           <?php echo $required ?> <?php echo $name_readonly ?>
                           class="pms_input frm_input required <?php echo $name_readonly ?>"
                           placeholder="이름">
                </div>

                <?php if ($req_nick): ?>
                <div class="pms_field">
                    <label for="reg_mb_nick" class="pms_label">
                        닉네임 <span class="pms_req">*</span>
                        <span class="pms_tip">앞으로 <?php echo (int)$config['cf_nick_modify'] ?>일간 변경 불가</span>
                    </label>
                    <input type="hidden" name="mb_nick_default" value="<?php echo isset($member['mb_nick']) ? get_text($member['mb_nick']) : '' ?>">
                    <input type="text" name="mb_nick" value="<?php echo isset($member['mb_nick']) ? get_text($member['mb_nick']) : '' ?>"
                           id="reg_mb_nick" required class="pms_input frm_input required nospace"
                           maxlength="20" placeholder="닉네임">
                    <span id="msg_mb_nick" class="pms_msg"></span>
                </div>
                <?php endif; ?>
            </div>

            <div class="pms_field">
                <label for="reg_mb_email" class="pms_label">
                    이메일 <span class="pms_req">*</span>
                    <?php if ($config['cf_use_email_certify']): ?>
                    <span class="pms_tip"><?php echo ($w == '') ? '이메일 인증 후 가입이 완료됩니다.' : '변경 시 재인증이 필요합니다.' ?></span>
                    <?php endif; ?>
                </label>
                <input type="hidden" name="old_email" value="<?php echo $member['mb_email'] ?>">
                <input type="text" name="mb_email" value="<?php echo isset($member['mb_email']) ? $member['mb_email'] : '' ?>"
                       id="reg_mb_email" required class="pms_input frm_input email required"
                       maxlength="100" placeholder="이메일 주소">
                <span id="msg_mb_email" class="pms_msg"></span>
            </div>

            <?php if ($config['cf_use_hp'] || ($config['cf_cert_use'] && ($config['cf_cert_hp'] || $config['cf_cert_simple']))): ?>
            <div class="pms_field">
                <label for="reg_mb_hp" class="pms_label">
                    휴대폰번호<?php if (!empty($hp_required)): ?> <span class="pms_req">*</span><?php endif; ?>
                    <?php echo $desc_phone ?>
                </label>
                <input type="text" name="mb_hp" value="<?php echo get_text($member['mb_hp']) ?>"
                       id="reg_mb_hp" <?php echo $hp_required ?> <?php echo $hp_readonly ?>
                       class="pms_input frm_input <?php echo $hp_required ?> <?php echo $hp_readonly ?>"
                       maxlength="20" placeholder="휴대폰번호 (예: 010-1234-5678)">
                <?php if ($config['cf_cert_use'] && ($config['cf_cert_hp'] || $config['cf_cert_simple'])): ?>
                <input type="hidden" name="old_mb_hp" value="<?php echo get_text($member['mb_hp']) ?>">
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ($config['cf_use_tel']): ?>
            <div class="pms_field">
                <label for="reg_mb_tel" class="pms_label">전화번호<?php if ($config['cf_req_tel']): ?> <span class="pms_req">*</span><?php endif; ?></label>
                <input type="text" name="mb_tel" value="<?php echo get_text($member['mb_tel']) ?>"
                       id="reg_mb_tel" <?php echo $config['cf_req_tel'] ? 'required' : '' ?>
                       class="pms_input frm_input <?php echo $config['cf_req_tel'] ? 'required' : '' ?>"
                       maxlength="20" placeholder="전화번호">
            </div>
            <?php endif; ?>

            <?php if ($config['cf_use_homepage']): ?>
            <div class="pms_field">
                <label for="reg_mb_homepage" class="pms_label">홈페이지<?php if ($config['cf_req_homepage']): ?> <span class="pms_req">*</span><?php endif; ?></label>
                <input type="text" name="mb_homepage" value="<?php echo get_text($member['mb_homepage']) ?>"
                       id="reg_mb_homepage" <?php echo $config['cf_req_homepage'] ? 'required' : '' ?>
                       class="pms_input frm_input <?php echo $config['cf_req_homepage'] ? 'required' : '' ?>"
                       maxlength="255" placeholder="https://">
            </div>
            <?php endif; ?>

            <?php if ($config['cf_use_addr']): ?>
            <div class="pms_field">
                <label class="pms_label">주소<?php if ($config['cf_req_addr']): ?> <span class="pms_req">*</span><?php endif; ?></label>
                <div class="pms_addr_row">
                    <input type="text" name="mb_zip" value="<?php echo $member['mb_zip1'].$member['mb_zip2'] ?>"
                           id="reg_mb_zip" <?php echo $config['cf_req_addr'] ? 'required' : '' ?>
                           class="pms_input frm_input <?php echo $config['cf_req_addr'] ? 'required' : '' ?>"
                           size="6" maxlength="6" placeholder="우편번호">
                    <button type="button" class="pms_btn_addr"
                            onclick="win_zip('fregisterform','mb_zip','mb_addr1','mb_addr2','mb_addr3','mb_addr_jibeon');">
                        주소 검색
                    </button>
                </div>
                <input type="text" name="mb_addr1" value="<?php echo get_text($member['mb_addr1']) ?>"
                       id="reg_mb_addr1" <?php echo $config['cf_req_addr'] ? 'required' : '' ?>
                       class="pms_input frm_input <?php echo $config['cf_req_addr'] ? 'required' : '' ?>"
                       placeholder="기본주소">
                <input type="text" name="mb_addr2" value="<?php echo get_text($member['mb_addr2']) ?>"
                       id="reg_mb_addr2" class="pms_input frm_input" placeholder="상세주소" style="margin-top:6px">
                <input type="text" name="mb_addr3" value="<?php echo get_text($member['mb_addr3']) ?>"
                       id="reg_mb_addr3" class="pms_input" readonly placeholder="참고항목" style="margin-top:6px">
                <input type="hidden" name="mb_addr_jibeon" value="<?php echo get_text($member['mb_addr_jibeon']) ?>">
            </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- ── 기타 설정 ── -->
    <?php if ($config['cf_use_signature'] || $config['cf_use_profile']): ?>
    <section class="pms_section">
        <h2 class="pms_section_title">기타 설정</h2>
        <div class="pms_fields">
            <?php if ($config['cf_use_signature']): ?>
            <div class="pms_field">
                <label for="reg_mb_signature" class="pms_label">서명<?php if ($config['cf_req_signature']): ?> <span class="pms_req">*</span><?php endif; ?></label>
                <textarea name="mb_signature" id="reg_mb_signature"
                          <?php echo $config['cf_req_signature'] ? 'required' : '' ?>
                          class="pms_textarea <?php echo $config['cf_req_signature'] ? 'required' : '' ?>"
                          placeholder="서명을 입력하세요"><?php echo $member['mb_signature'] ?></textarea>
            </div>
            <?php endif; ?>
            <?php if ($config['cf_use_profile']): ?>
            <div class="pms_field">
                <label for="reg_mb_profile" class="pms_label">자기소개</label>
                <textarea name="mb_profile" id="reg_mb_profile"
                          class="pms_textarea"
                          placeholder="자기소개를 입력하세요"><?php echo $member['mb_profile'] ?></textarea>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── 약관 동의 ── -->
    <?php if (isset($agree_html) && $agree_html): ?>
    <section class="pms_section">
        <h2 class="pms_section_title">약관 동의</h2>
        <?php echo $agree_html ?>
    </section>
    <?php endif; ?>

    <!-- ── 캡차 ── -->
    <div class="pms_captcha"><?php echo captcha_html() ?></div>

    <!-- ── 제출 ── -->
    <div class="pms_actions">
        <button type="submit" id="btn_submit" class="pms_btn_submit">
            <?php echo ($w == 'u') ? '정보 저장' : '회원가입' ?>
        </button>
        <?php if ($w == 'u'): ?>
        <a href="<?php echo G5_URL ?>" class="pms_btn_cancel">취소</a>
        <?php else: ?>
        <a href="<?php echo G5_BBS_URL ?>/login.php" class="pms_btn_cancel">로그인으로</a>
        <?php endif; ?>
    </div>

</div><!-- //pms_card -->
</form>
</div><!-- //pms_wrap -->

<script>
jQuery(function($) {
    $(document).on("click", ".tooltip_icon", function(e) {
        $(this).next(".tooltip").fadeIn(400).css("display","inline-block");
    }).on("mouseout", ".tooltip_icon", function() {
        $(this).next(".tooltip").fadeOut();
    });
});

function fregisterform_submit(f) {
    if (f.w.value == "") {
        var msg = reg_mb_id_check();
        if (msg) { alert(msg); f.mb_id.select(); return false; }
    }
    if (f.w.value == "" && f.mb_password.value.length < 3) {
        alert("비밀번호를 3글자 이상 입력하십시오."); f.mb_password.focus(); return false;
    }
    if (f.mb_password.value != f.mb_password_re.value) {
        alert("비밀번호가 같지 않습니다."); f.mb_password_re.focus(); return false;
    }
    if (f.mb_password.value.length > 0 && f.mb_password_re.value.length < 3) {
        alert("비밀번호를 3글자 이상 입력하십시오."); f.mb_password_re.focus(); return false;
    }
    if (f.w.value == "" && f.mb_name.value.length < 1) {
        alert("이름을 입력하십시오."); f.mb_name.focus(); return false;
    }
    <?php if ($w == '' && $config['cf_cert_use'] && $config['cf_cert_req']): ?>
    if (f.cert_no.value == "") { alert("회원가입을 위해서는 본인확인을 해주셔야 합니다."); return false; }
    <?php endif; ?>
    if ((f.w.value == "") || (f.w.value == "u" && f.mb_nick && f.mb_nick.defaultValue != f.mb_nick.value)) {
        if (typeof reg_mb_nick_check === "function") {
            var msg = reg_mb_nick_check();
            if (msg) { alert(msg); f.reg_mb_nick && f.reg_mb_nick.select(); return false; }
        }
    }
    if ((f.w.value == "") || (f.w.value == "u" && f.mb_email.defaultValue != f.mb_email.value)) {
        var msg = reg_mb_email_check();
        if (msg) { alert(msg); f.reg_mb_email && f.reg_mb_email.select(); return false; }
    }
    <?php if (($config['cf_use_hp'] || $config['cf_cert_hp']) && $config['cf_req_hp']): ?>
    if (typeof reg_mb_hp_check === "function") {
        var msg = reg_mb_hp_check();
        if (msg) { alert(msg); f.reg_mb_hp && f.reg_mb_hp.select(); return false; }
    }
    <?php endif; ?>
    <?php echo chk_captcha_js(); ?>
    document.getElementById("btn_submit").disabled = "disabled";
    return true;
}
</script>

<?php if ($config['cf_cert_use']): ?>
<script>
jQuery(function($) {
    var pageTypeParam = "bo_table=&wr_id=&skin_dir=";
    <?php if ($config['cf_cert_simple']): ?>
    $("#win_sa_kakao_cert").click(function() {
        if (!cert_confirm()) return false;
        certify_win_open("kakao-simple", "<?php echo G5_KAKAO_URL ?>/cert.php?" + pageTypeParam);
    });
    <?php endif; ?>
    <?php if ($config['cf_cert_hp']): ?>
    $("#win_hp_cert").click(function() {
        if (!cert_confirm()) return false;
        var params = "?" + pageTypeParam;
        <?php
        switch($config['cf_cert_hp']) {
            case 'kcb':  echo 'certify_win_open("kcb-hp","'.G5_OKNAME_URL.'/hpcert1.php"+params);'; break;
            case 'kcp':  echo 'certify_win_open("kcp-hp","'.G5_KCPCERT_URL.'/kcpcert_form.php"+params);'; break;
            case 'lg':   echo 'certify_win_open("lg-hp","'.G5_LGXPAY_URL.'/AuthOnlyReq.php"+params);'; break;
            default: echo 'alert("휴대폰 본인확인 설정을 확인해주세요."); return false;'; break;
        }
        ?>
    });
    <?php endif; ?>
    <?php if ($config['cf_cert_ipin']): ?>
    $("#win_ipin_cert").click(function() {
        if (!cert_confirm()) return false;
        certify_win_open("ipin", "<?php echo G5_IPIN_URL ?>/ipin.php?" + pageTypeParam);
    });
    <?php endif; ?>
});
</script>
<?php endif; ?>
<!-- } 회원가입 끝 -->

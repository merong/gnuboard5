<?php
if (!defined('_GNUBOARD_')) exit;
echo '<link rel="stylesheet" href="'.G5_THEME_URL.'/skin/member/portal/style.css">';

$use_cert = !empty($config['cf_cert_use']) && !empty($config['cf_cert_find']);
$use_cert = $use_cert && (!empty($config['cf_cert_simple']) || !empty($config['cf_cert_hp']) || !empty($config['cf_cert_ipin']));

if ($use_cert) {
    echo '<script src="'.G5_JS_URL.'/certify.js?v='.G5_JS_VER.'"></script>';
}
?>

<div class="pms_wrap">
<div class="pms_card">

    <div class="pms_logo">
        <a href="<?php echo G5_URL ?>">
            <span class="pms_logo_text"><?php echo htmlspecialchars($config['cf_title']) ?></span>
        </a>
    </div>

    <h1 class="pms_title">회원정보 찾기</h1>

    <?php if ($use_cert): ?>
    <div class="pfl_tabs" role="tablist">
        <button type="button" class="pfl_tab pfl_tab_on" id="pfl_tab_email" role="tab" aria-selected="true" aria-controls="pfl_panel_email">이메일로 찾기</button>
        <button type="button" class="pfl_tab" id="pfl_tab_cert" role="tab" aria-selected="false" aria-controls="pfl_panel_cert">본인인증으로 찾기</button>
    </div>
    <?php endif; ?>

    <!-- 이메일로 찾기 -->
    <div id="pfl_panel_email" role="tabpanel">
        <p class="pfl_desc">가입 시 등록한 이메일 주소를 입력하시면 아이디와 임시 비밀번호를 보내드립니다.</p>
        <form name="fpasswordlost" action="<?php echo $action_url ?>" onsubmit="return fpasswordlost_submit(this);" method="post" autocomplete="off">
            <input type="hidden" name="cert_no" value="">
            <div class="pms_field">
                <label for="mb_email" class="pms_label">이메일 주소 <span class="pms_req">*</span></label>
                <input type="email" name="mb_email" id="mb_email" required class="pms_input" placeholder="example@email.com" autofocus>
            </div>
            <div class="pms_captcha"><?php echo captcha_html(); ?></div>
            <div class="pms_actions">
                <button type="submit" class="pms_btn_submit">인증 메일 보내기</button>
                <a href="<?php echo G5_BBS_URL ?>/login.php" class="pms_btn_cancel">로그인으로 돌아가기</a>
            </div>
        </form>
    </div>

    <?php if ($use_cert): ?>
    <!-- 본인인증으로 찾기 -->
    <div id="pfl_panel_cert" role="tabpanel" style="display:none">
        <p class="pfl_desc">본인인증을 통해 아이디와 비밀번호를 찾을 수 있습니다.</p>
        <div class="pms_cert_btns">
            <?php if (!empty($config['cf_cert_simple'])): ?>
            <button type="button" id="win_sa_kakao_cert" class="pms_btn_cert win_sa_cert" data-type=""><i class="fa fa-mobile"></i> 간편인증</button>
            <?php endif; ?>
            <?php if (!empty($config['cf_cert_hp'])): ?>
            <button type="button" id="win_hp_cert" class="pms_btn_cert"><i class="fa fa-mobile"></i> 휴대폰 본인확인</button>
            <?php endif; ?>
            <?php if (!empty($config['cf_cert_ipin'])): ?>
            <button type="button" id="win_ipin_cert" class="pms_btn_cert"><i class="fa fa-id-card"></i> 아이핀 본인확인</button>
            <?php endif; ?>
        </div>
        <p class="pms_cert_desc">인증 후 자동으로 회원정보가 안내됩니다.</p>
        <div class="pms_actions">
            <a href="<?php echo G5_BBS_URL ?>/login.php" class="pms_btn_cancel">로그인으로 돌아가기</a>
        </div>
    </div>
    <?php endif; ?>

</div>
</div>

<script>
jQuery(function($) {
    <?php if ($use_cert): ?>
    // 탭 전환
    $(".pfl_tab").on("click", function() {
        var target = $(this).attr("aria-controls");
        $(".pfl_tab").removeClass("pfl_tab_on").attr("aria-selected", "false");
        $(this).addClass("pfl_tab_on").attr("aria-selected", "true");
        $("#pfl_panel_email, #pfl_panel_cert").hide();
        $("#" + target).show();
    });

    <?php if (!empty($config['cf_cert_simple'])): ?>
    var sa_url = "<?php echo G5_INICERT_URL; ?>/ini_request.php";
    $(".win_sa_cert").on("click", function() {
        var type = $(this).data("type");
        call_sa(sa_url + "?directAgency=" + type + "&pageType=find");
    });
    <?php endif; ?>

    <?php if (!empty($config['cf_cert_ipin'])): ?>
    $("#win_ipin_cert").on("click", function() {
        certify_win_open("kcb-ipin", "<?php echo G5_OKNAME_URL; ?>/ipin1.php?pageType=find");
    });
    <?php endif; ?>

    <?php if (!empty($config['cf_cert_hp'])): ?>
    $("#win_hp_cert").on("click", function() {
        <?php
        switch ($config['cf_cert_hp']) {
            case 'kcb': $cert_url = G5_OKNAME_URL.'/hpcert1.php'; $cert_type = 'kcb-hp'; break;
            case 'kcp': $cert_url = G5_KCPCERT_URL.'/kcpcert_form.php'; $cert_type = 'kcp-hp'; break;
            case 'lg':  $cert_url = G5_LGXPAY_URL.'/AuthOnlyReq.php'; $cert_type = 'lg-hp'; break;
            default: echo 'alert("기본환경설정에서 휴대폰 본인확인 설정을 해주십시오."); return;';
        }
        ?>
        certify_win_open("<?php echo $cert_type; ?>", "<?php echo $cert_url; ?>?pageType=find");
    });
    <?php endif; ?>
    <?php endif; ?>
});

function fpasswordlost_submit(f) {
    <?php echo chk_captcha_js(); ?>
    return true;
}
</script>

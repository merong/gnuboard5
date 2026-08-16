<?php
if (!defined('_GNUBOARD_')) exit;
add_stylesheet('<link rel="stylesheet" href="'.G5_THEME_URL.'/skin/member/portal/style.css?v='.filemtime(G5_THEME_PATH.'/skin/member/portal/style.css').'">', 0);
?>

<!-- 회원가입결과 시작 { -->
<div class="pms_wrap">
    <div class="pms_card">

        <div class="pms_logo">
            <a href="<?php echo G5_URL ?>">
                <img src="<?php echo G5_THEME_URL ?>/img/moidam-logo.svg" alt="모담" class="pms_logo_img">
            </a>
        </div>

        <h1 class="pms_title">가입 완료</h1>

        <p class="pms_result_congrats">
            <strong><?php echo get_text($mb['mb_name']); ?></strong>님, 모담 가입을 진심으로 환영합니다.
        </p>

        <?php if (is_use_email_certify()) { ?>
        <p class="pms_result_txt">
            입력하신 이메일로 인증 메일을 보냈습니다. 메일의 인증 링크를 확인하시면 서비스를 이용하실 수 있습니다.
        </p>
        <div class="pms_result_meta">
            <div><span>아이디</span><strong><?php echo $mb['mb_id'] ?></strong></div>
            <div><span>이메일</span><strong><?php echo $mb['mb_email'] ?></strong></div>
        </div>
        <p class="pms_result_txt">이메일을 잘못 입력하셨다면 관리자에게 문의해 주세요.</p>
        <?php } ?>

        <p class="pms_result_txt">
            비밀번호는 암호화되어 저장됩니다. 아이디·비밀번호를 잊으시면 가입 시 등록한 이메일로 찾을 수 있습니다.
        </p>

        <div class="pms_actions">
            <a href="<?php echo G5_URL ?>" class="pms_btn_submit">메인으로</a>
        </div>

    </div>
</div>
<!-- } 회원가입결과 끝 -->

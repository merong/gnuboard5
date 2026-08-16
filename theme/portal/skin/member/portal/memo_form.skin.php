<?php
if (!defined('_GNUBOARD_')) exit;
$_pmm_css = G5_THEME_PATH.'/skin/member/portal/style.css';
echo '<link rel="stylesheet" href="'.G5_THEME_URL.'/skin/member/portal/style.css?v='.filemtime($_pmm_css).'">'.PHP_EOL;
?>
<div class="pmm_wrap">
    <div class="pmm_card">

        <div class="pmm_head">
            <h1 class="pmm_title">쪽지 보내기</h1>
            <button type="button" class="pmm_close" onclick="pmmCloseWin()" aria-label="닫기">
                <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false">
                    <circle cx="12" cy="12" r="12" fill="#1a1a1a"/>
                    <path d="M8 8l8 8M16 8l-8 8" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <nav class="pmm_tabs" aria-label="쪽지함 메뉴">
            <a href="./memo.php?kind=recv" class="pmm_tab">받은쪽지</a>
            <a href="./memo.php?kind=send" class="pmm_tab">보낸쪽지</a>
            <a href="./memo_form.php" class="pmm_tab_write" aria-current="page">쓰기</a>
        </nav>

        <form name="fmemoform" action="<?php echo $memo_action_url ?>"
              onsubmit="return fmemoform_submit(this);" method="post" autocomplete="off"
              class="pmm_form">

            <div class="pmm_field">
                <label class="pmm_label" for="me_recv_mb_id">받는 사람 <span class="pmm_req" aria-hidden="true">*</span></label>
                <input type="text" name="me_recv_mb_id" id="me_recv_mb_id" required
                       value="<?php echo $me_recv_mb_id ?>"
                       class="pmm_input" placeholder="회원 아이디 (여러 명은 콤마로 구분)" maxlength="255">
                <p class="pmm_tip">여러 회원에게 보낼 때는 콤마(,)로 구분하세요.<?php
                    if ($config['cf_memo_send_point']) {
                        echo ' 1인당 '.number_format($config['cf_memo_send_point']).'P가 차감됩니다.';
                    }
                ?></p>
            </div>

            <div class="pmm_field pmm_field_grow">
                <label class="pmm_label" for="me_memo">내용 <span class="pmm_req" aria-hidden="true">*</span></label>
                <textarea name="me_memo" id="me_memo" required
                          class="pmm_textarea" placeholder="쪽지 내용을 입력하세요."><?php echo $content ?></textarea>
            </div>

            <?php $captcha = captcha_html(); if ($captcha) { ?>
            <div class="pmm_field pmm_captcha">
                <?php echo $captcha ?>
            </div>
            <?php } ?>

            <div class="pmm_actions">
                <button type="submit" id="btn_submit" class="pmm_btn pmm_btn_primary">보내기</button>
                <a href="./memo.php?kind=recv" class="pmm_btn pmm_btn_outline">목록</a>
            </div>
        </form>

    </div>
</div>
<script>
function pmmCloseWin() {
    if (window.opener) { window.close(); return; }
    history.length > 1 ? history.back() : (location.href = '<?php echo G5_URL ?>');
}
function fmemoform_submit(f) {
    <?php echo chk_captcha_js() ?>
    return true;
}
</script>

<?php
if (!defined('_GNUBOARD_')) exit;
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css?v='.filemtime(__DIR__.'/style.css').'">', 0);
?>
<script>document.documentElement.classList.add('popup_page'); document.addEventListener('DOMContentLoaded',function(){ document.body.classList.add('popup_page'); });</script>
<style>
html, body { margin: 0; padding: 0; background: #fff; }
/* 팝업 페이지: popup_win 외 body 직계 요소(탑배너, 로그인메시지 등) 숨김 */
body > *:not(.popup_win) { display: none !important; }
</style>

<div class="popup_win" id="formmail">

    <!-- 헤더 -->
    <div class="popup_header">
        <span class="popup_header_icon"><i class="fa fa-envelope-o"></i></span>
        <h1 class="popup_header_title"><?php echo htmlspecialchars($name) ?>님께 메일보내기</h1>
        <button type="button" class="popup_close_x" onclick="window.close()" title="창 닫기">&#215;</button>
    </div>

    <!-- 본문 -->
    <div class="popup_body">
        <form name="fformmail" id="fformmail" action="./formmail_send.php" onsubmit="return fformmail_submit(this);" method="post" enctype="multipart/form-data">
            <input type="hidden" name="to"     value="<?php echo $email ?>">
            <input type="hidden" name="attach" value="2">
            <?php if ($is_member) { ?>
            <input type="hidden" name="fnick" value="<?php echo get_text($member['mb_nick']) ?>">
            <input type="hidden" name="fmail"  value="<?php echo $member['mb_email'] ?>">
            <?php } ?>

            <div class="fm_fields">
                <?php if (!$is_member) { ?>
                <div class="fm_field">
                    <label class="fm_label" for="fnick">이름 <span style="color:#03c75a">*</span></label>
                    <input type="text" name="fnick" id="fnick" required class="fm_input" placeholder="이름을 입력하세요">
                </div>
                <div class="fm_field">
                    <label class="fm_label" for="fmail">이메일 <span style="color:#03c75a">*</span></label>
                    <input type="text" name="fmail" id="fmail" required class="fm_input" placeholder="이메일 주소를 입력하세요">
                </div>
                <?php } ?>

                <div class="fm_field">
                    <label class="fm_label" for="subject">제목 <span style="color:#03c75a">*</span></label>
                    <input type="text" name="subject" id="subject" required class="fm_input" placeholder="메일 제목을 입력하세요">
                </div>

                <div class="fm_field">
                    <label class="fm_label">형식</label>
                    <div class="fm_type_row">
                        <label class="fm_type_label">
                            <input type="radio" name="type" value="0" <?php echo $type_checked[0] ?>>TEXT
                        </label>
                        <label class="fm_type_label">
                            <input type="radio" name="type" value="1" <?php echo $type_checked[1] ?>>HTML
                        </label>
                        <label class="fm_type_label">
                            <input type="radio" name="type" value="2" <?php echo $type_checked[2] ?>>TEXT+HTML
                        </label>
                    </div>
                </div>

                <div class="fm_field">
                    <label class="fm_label" for="content">내용 <span style="color:#03c75a">*</span></label>
                    <textarea name="content" id="content" required class="fm_textarea" placeholder="내용을 입력하세요"></textarea>
                </div>

                <div class="fm_field">
                    <label class="fm_label">첨부파일</label>
                    <div class="fm_file_row">
                        <span class="fm_file_icon"><i class="fa fa-paperclip"></i></span>
                        <input type="file" name="file1" id="file1" class="fm_file_input">
                    </div>
                    <div class="fm_file_row" style="margin-top:6px">
                        <span class="fm_file_icon"><i class="fa fa-paperclip"></i></span>
                        <input type="file" name="file2" id="file2" class="fm_file_input">
                    </div>
                    <p class="fm_attach_info"><i class="fa fa-info-circle"></i> 첨부파일은 누락될 수 있으므로 발송 후 첨부 여부를 반드시 확인해 주세요.</p>
                </div>

                <div class="fm_field fm_captcha">
                    <?php echo captcha_html(); ?>
                </div>
            </div>

            <!-- 하단 버튼 (form 안) -->
            <div class="popup_footer" style="margin:0 -20px">
                <button type="button" class="popup_btn_cancel" onclick="window.close()">
                    <i class="fa fa-times"></i> 닫기
                </button>
                <button type="submit" id="btn_submit" class="popup_btn_primary">
                    <i class="fa fa-paper-plane-o"></i> 메일발송
                </button>
            </div>
        </form>
    </div>

</div>

<script>
(function(){
    var f = document.fformmail;
    if (f) {
        if (typeof f.fname !== 'undefined') f.fname.focus();
        else if (f.subject) f.subject.focus();
    }
})();

function fformmail_submit(f) {
    <?php echo chk_captcha_js(); ?>
    if (f.file1.value || f.file2.value) {
        if (!confirm("첨부파일 용량이 큰 경우 전송 시간이 오래 걸립니다.\n발송 완료 전에 창을 닫거나 새로고침하지 마세요."))
            return false;
    }
    document.getElementById('btn_submit').disabled = true;
    return true;
}
</script>

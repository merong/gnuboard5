<?php if (!defined('_GNUBOARD_')) exit;

/*
 * 게시물별 다운로드 포인트 옵션
 * - 여분 필드 wr_1 에 저장 (write_update.php 가 wr_1~wr_10 을 $_POST 값 그대로 자동 저장)
 * - 실제 차감 반영은 extend/cafe_style_download_point.extend.php 에서 $board['bo_download_point'] 를
 *   덮어써 처리(스킨과 무관하게 모든 게시판에 공통 적용)
 * - 비워두면 게시판 기본값(bo_download_point)을 그대로 사용
 */
$_wr_download_point = ($w == 'u' && isset($write['wr_1'])) ? $write['wr_1'] : '';

$option        = '';
$option_hidden = '';
if ($is_notice || $is_html || $is_secret || $is_mail) {
    if ($is_notice) $option .= '<li class="chk_box"><input type="checkbox" id="notice" name="notice" class="selec_chk" value="1" '.$notice_checked.'><label for="notice"><span></span>공지</label></li>';
    if ($is_html) {
        if ($is_dhtml_editor) $option_hidden .= '<input type="hidden" value="html1" name="html">';
        else $option .= '<li class="chk_box"><input type="checkbox" id="html" name="html" onclick="html_auto_br(this);" class="selec_chk" value="'.$html_value.'" '.$html_checked.'><label for="html"><span></span>HTML</label></li>';
    }
    if ($is_secret) {
        if ($is_admin || $is_secret == 1) $option .= '<li class="chk_box"><input type="checkbox" id="secret" name="secret" class="selec_chk" value="secret" '.$secret_checked.'><label for="secret"><span></span>비밀글</label></li>';
        else $option_hidden .= '<input type="hidden" name="secret" value="secret">';
    }
    if ($is_mail) $option .= '<li class="chk_box"><input type="checkbox" id="mail" name="mail" class="selec_chk" value="mail" '.$recv_email_checked.'><label for="mail"><span></span>답변메일받기</label></li>';
}

$_wt_mode  = ($w == 'u') ? '글 수정' : (($w == 'r') ? '답글 쓰기' : '글쓰기');
$_bo_label = get_text($board['bo_subject']);
$_list_url = get_pretty_url($bo_table);
?>

<section id="bsk_write_wrap">

    <!-- 페이지 헤더 -->
    <div class="bsk_write_header">
        <nav class="bsk_write_bc" aria-label="breadcrumb">
            <a href="<?php echo G5_URL ?>">홈</a>
            <span class="bc_sep">&rsaquo;</span>
            <a href="<?php echo $_list_url ?>"><?php echo $_bo_label ?></a>
            <span class="bc_sep">&rsaquo;</span>
            <span class="bc_current"><?php echo $_wt_mode ?></span>
        </nav>
        <h2 class="bsk_write_title"><?php echo $_wt_mode ?></h2>
    </div>

    <!-- 폼 카드 -->
    <div class="bsk_write_card">
    <form name="fwrite" id="fwrite" action="<?php echo $action_url ?>" onsubmit="return fwrite_submit(this);"
          method="post" enctype="multipart/form-data" autocomplete="off">
    <?php echo $option_hidden; ?>
    <input type="hidden" name="uid"      value="<?php echo get_uniqid() ?>">
    <input type="hidden" name="w"        value="<?php echo $w ?>">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id"    value="<?php echo $wr_id ?>">
    <input type="hidden" name="sca"      value="<?php echo $sca ?>">
    <input type="hidden" name="sfl"      value="<?php echo $sfl ?>">
    <input type="hidden" name="stx"      value="<?php echo $stx ?>">
    <input type="hidden" name="spt"      value="<?php echo $spt ?>">
    <input type="hidden" name="sst"      value="<?php echo $sst ?>">
    <input type="hidden" name="sod"      value="<?php echo $sod ?>">
    <input type="hidden" name="page"     value="<?php echo $page ?>">

    <?php if ($is_category) { ?>
    <div class="bsk_write_row">
        <label for="ca_name" class="bsk_write_label">분류</label>
        <div class="bsk_write_field">
            <select name="ca_name" id="ca_name" required class="bsk_select bsk_select_arrow">
                <option value="">분류를 선택하세요</option>
                <?php echo $category_option ?>
            </select>
        </div>
    </div>
    <?php } ?>

    <?php if ($is_name || $is_password || $is_email || $is_homepage) { ?>
    <div class="bsk_write_row">
        <label class="bsk_write_label">작성자 정보</label>
        <div class="bsk_write_field bsk_write_inline">
            <?php if ($is_name) { ?>
            <input type="text" name="wr_name" value="<?php echo $name ?>" id="wr_name" required class="bsk_input bsk_input_sm" placeholder="이름">
            <?php } ?>
            <?php if ($is_password) { ?>
            <input type="password" name="wr_password" id="wr_password" <?php echo $password_required ?> class="bsk_input bsk_input_sm" placeholder="비밀번호">
            <?php } ?>
            <?php if ($is_email) { ?>
            <input type="text" name="wr_email" value="<?php echo $email ?>" id="wr_email" class="bsk_input bsk_input_sm" placeholder="이메일">
            <?php } ?>
            <?php if ($is_homepage) { ?>
            <input type="text" name="wr_homepage" value="<?php echo $homepage ?>" id="wr_homepage" class="bsk_input bsk_input_sm" placeholder="https://">
            <?php } ?>
        </div>
    </div>
    <?php } ?>

    <?php if ($option) { ?>
    <div class="bsk_write_row">
        <label class="bsk_write_label">옵션</label>
        <div class="bsk_write_field">
            <ul class="bsk_write_options"><?php echo $option ?></ul>
        </div>
    </div>
    <?php } ?>

    <!-- 제목 + 임시저장 -->
    <div class="bsk_write_row bsk_write_row_subject">
        <label for="wr_subject" class="bsk_write_label">제목 <span class="bsk_required">*</span></label>
        <div class="bsk_write_field">
            <div id="autosave_wrapper">
                <input type="text" name="wr_subject" value="<?php echo $subject ?>" id="wr_subject" required
                       class="bsk_input bsk_input_full" placeholder="제목을 입력해주세요" maxlength="255">
                <?php if ($is_member) { ?>
                <script src="<?php echo G5_JS_URL ?>/autosave.js"></script>
                <?php if ($editor_content_js) echo $editor_content_js; ?>
                <button type="button" id="btn_autosave" class="bsk_btn bsk_btn_autosave">
                    <i class="fa fa-floppy-o"></i>
                    임시저장 <span class="bsk_autosave_cnt">(<span id="autosave_count"><?php echo $autosave_count ?></span>)</span>
                </button>
                <div id="autosave_pop" class="bsk_autosave_pop">
                    <strong>임시 저장된 글</strong>
                    <ul></ul>
                    <div class="bsk_autosave_foot">
                        <button type="button" class="autosave_close bsk_btn bsk_btn_sm">닫기</button>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- 본문 에디터 -->
    <div class="bsk_write_row bsk_write_row_content">
        <label for="wr_content" class="bsk_write_label">내용 <span class="bsk_required">*</span></label>
        <div class="bsk_write_field">
        <?php if ($write_min || $write_max) { ?>
        <p class="bsk_charcount_desc">최소 <strong><?php echo $write_min ?></strong>자 이상, 최대 <strong><?php echo $write_max ?></strong>자 이하</p>
        <?php } ?>
        <div class="wr_content <?php echo $is_dhtml_editor ? $config['cf_editor'] : '' ?>">
            <?php echo $editor_html ?>
        </div>
        <?php if ($write_min || $write_max) { ?>
        <div class="bsk_charcount"><span id="char_count"></span>글자</div>
        <?php } ?>
        </div>
    </div>

    <?php for ($i = 1; $is_link && $i <= G5_LINK_COUNT; $i++) { ?>
    <div class="bsk_write_row">
        <label for="wr_link<?php echo $i ?>" class="bsk_write_label"><i class="fa fa-link"></i> 링크 <?php echo $i ?></label>
        <div class="bsk_write_field">
            <input type="text" name="wr_link<?php echo $i ?>"
                   value="<?php if ($w == 'u') echo $write['wr_link'.$i] ?>"
                   id="wr_link<?php echo $i ?>" class="bsk_input bsk_input_full" placeholder="https://">
        </div>
    </div>
    <?php } ?>

    <?php for ($i = 0; $is_file && $i < $file_count; $i++) { ?>
    <div class="bsk_write_row bsk_write_file_row">
        <label for="bf_file_<?php echo $i+1 ?>" class="bsk_write_label"><i class="fa fa-paperclip"></i> 첨부파일 <?php echo $i+1 ?></label>
        <div class="bsk_write_field bsk_write_file_inner">
            <label class="bsk_file_label">
                <i class="fa fa-folder-open-o"></i> 파일 선택
                <input type="file" name="bf_file[]" id="bf_file_<?php echo $i+1 ?>"
                       class="bsk_file_input" title="<?php echo $upload_max_filesize ?> 이하">
            </label>
            <span class="bsk_file_limit"><?php echo $upload_max_filesize ?> 이하</span>
            <?php if ($is_file_content) { ?>
            <input type="text" name="bf_content[]" value="<?php echo ($w == 'u') ? $file[$i]['bf_content'] : '' ?>"
                   class="bsk_input" placeholder="파일 설명 (선택)">
            <?php } ?>
            <?php if ($w == 'u' && $file[$i]['file']) { ?>
            <div class="bsk_file_del">
                <input type="checkbox" id="bf_file_del<?php echo $i ?>" name="bf_file_del[<?php echo $i ?>]" value="1">
                <label for="bf_file_del<?php echo $i ?>">
                    <i class="fa fa-times-circle"></i> <?php echo $file[$i]['source'].' ('.$file[$i]['size'].')' ?> 삭제
                </label>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php } ?>

    <?php if ($is_file) { ?>
    <div class="bsk_write_row">
        <label for="wr_1" class="bsk_write_label"><i class="fa fa-coins"></i> 다운로드 포인트</label>
        <div class="bsk_write_field">
            <input type="number" name="wr_1" id="wr_1" value="<?php echo htmlspecialchars($_wr_download_point) ?>"
                   class="bsk_input bsk_input_sm" placeholder="예: -20" step="1">
            <p class="bsk_write_hint" style="margin-top:6px;font-size:13px;color:#888;">
                이 글의 첨부파일을 내려받을 때 차감(또는 지급)할 포인트입니다. 음수를 입력하면 다운로드 시 포인트가 차감됩니다.
                비워두면 게시판 기본 다운로드 포인트(<?php echo number_format($board['bo_download_point']) ?>P)가 적용됩니다.
            </p>
        </div>
    </div>
    <?php } ?>

    <?php if ($is_use_captcha) { ?>
    <div class="bsk_write_row">
        <label class="bsk_write_label">보안문자</label>
        <div class="bsk_write_field"><?php echo $captcha_html ?></div>
    </div>
    <?php } ?>

    <!-- 제출 버튼 -->
    <div class="bsk_write_submit">
        <a href="<?php echo $_list_url ?>" class="bsk_btn bsk_btn_cancel">
            <i class="fa fa-list"></i> 목록
        </a>
        <button type="submit" id="btn_submit" class="bsk_btn bsk_btn_submit">
            <i class="fa fa-check"></i>
            <?php echo ($w == 'u') ? '수정 완료' : '등록하기' ?>
        </button>
    </div>

    </form>
    </div><!-- /.bsk_write_card -->

</section>

<script>
<?php if ($write_min || $write_max) { ?>
var char_min = parseInt(<?php echo $write_min ?>);
var char_max = parseInt(<?php echo $write_max ?>);
check_byte('wr_content', 'char_count');
$(function() { $('#wr_content').on('keyup', function() { check_byte('wr_content', 'char_count'); }); });
<?php } ?>

function html_auto_br(obj) {
    if (obj.checked) {
        if (confirm('자동 줄바꿈을 사용하시겠습니까?')) obj.value = 'html2'; else obj.value = 'html1';
    } else { obj.value = ''; }
}

function fwrite_submit(f) {
    <?php echo $editor_js ?>
    var subject = '', content = '';
    $.ajax({
        url: g5_bbs_url+'/ajax.filter.php', type: 'POST',
        data: { subject: f.wr_subject.value, content: f.wr_content ? f.wr_content.value : '' },
        dataType: 'json', async: false, cache: false,
        success: function(data) { subject = data.subject; content = data.content; }
    });
    if (subject) { alert("제목에 금지단어('"+subject+"')가 포함되어있습니다"); return false; }
    if (content) { alert("내용에 금지단어('"+content+"')가 포함되어있습니다"); return false; }
    <?php if ($write_min || $write_max) { ?>
    check_byte('wr_content', 'char_count');
    var cnt = parseInt(document.getElementById('char_count').innerHTML);
    if (char_min > 0 && char_min > cnt) { alert('내용은 '+char_min+'글자 이상 입력하세요.'); return false; }
    if (char_max > 0 && char_max < cnt) { alert('내용은 '+char_max+'글자 이하로 입력하세요.'); return false; }
    <?php } ?>
    <?php if ($is_use_captcha) echo chk_captcha_js() ?>
    document.getElementById('btn_submit').disabled = 'disabled';
    return true;
}

/* 파일 input 선택 파일명 표시 */
$(function() {
    $(document).on('change', '.bsk_file_input', function() {
        var name = this.files.length ? this.files[0].name : '파일 선택';
        $(this).closest('.bsk_file_label').find('i').after(' <span class="bsk_file_chosen">'+name+'</span>');
        $(this).closest('.bsk_file_label').find('.bsk_file_chosen').text(name);
    });
});
</script>

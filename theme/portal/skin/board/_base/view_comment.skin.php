<?php if (!defined('_GNUBOARD_')) exit; ?>
<script>
var char_min = parseInt(<?php echo $comment_min ?>);
var char_max = parseInt(<?php echo $comment_max ?>);
</script>

<button type="button" class="bsk_cmt_toggle">
    <i class="fa fa-comment-o"></i> 댓글 <strong><?php echo $view['wr_comment'] ?></strong>
</button>

<section id="bo_vc" class="bsk_cmt_section">
    <h2 class="sound_only">댓글목록</h2>
    <?php
    $cmt_amt = count($list);
    for ($i = 0; $i < $cmt_amt; $i++) {
        $comment_id   = $list[$i]['wr_id'];
        $cmt_depth    = strlen($list[$i]['wr_comment_reply']) * 40;
        $comment      = $list[$i]['content'];
        $comment      = preg_replace("/\[\<a\s.*href\=\"(http|https|ftp|mms)\:\/\/([^[:space:]]+)\.(mp3|wma|wmv|asf|asx|mpg|mpeg)\".*\<\/a\>\]/i", "<script>doc_write(obj_movie('$1://$2.$3'));<\/script>", $comment);
        $cmt_sv       = $cmt_amt - $i + 1;
        $c_reply_href = $comment_common_url.'&amp;c_id='.$comment_id.'&amp;w=c#bo_vc_w';
        $c_edit_href  = $comment_common_url.'&amp;c_id='.$comment_id.'&amp;w=cu#bo_vc_w';
        $is_cmt_ctrl  = ($list[$i]['is_reply'] || $list[$i]['is_edit'] || $list[$i]['is_del']) ? 1 : 0;
    ?>
    <article id="c_<?php echo $comment_id ?>" class="bsk_cmt_item<?php echo $cmt_depth ? ' bsk_cmt_reply' : '' ?>"
             <?php if ($cmt_depth) echo 'style="margin-left:'.$cmt_depth.'px"'; ?>>
        <div class="bsk_cmt_avatar"><?php echo get_member_profile_img($list[$i]['mb_id']) ?></div>
        <div class="bsk_cmt_body">
            <header class="bsk_cmt_header" style="z-index:<?php echo $cmt_sv ?>">
                <span class="sv_use bsk_cmt_name"><?php echo $list[$i]['name'] ?></span>
                <?php if ($is_ip_view) { ?><span class="bsk_cmt_ip">(<?php echo $list[$i]['ip'] ?>)</span><?php } ?>
                <time class="bsk_cmt_date"><?php echo $list[$i]['datetime'] ?></time>
                <?php include(G5_SNS_PATH.'/view_comment_list.sns.skin.php') ?>
            </header>
            <div class="bsk_cmt_content">
                <?php if (strstr($list[$i]['wr_option'], 'secret')) { ?>
                <i class="fa fa-lock bsk_icon_lock" title="비밀글"></i>
                <?php } ?>
                <?php echo $comment ?>
            </div>
            <?php if ($is_cmt_ctrl) {
                if ($w == 'cu') {
                    $sql = " select wr_id, wr_content, mb_id from $write_table where wr_id = '$c_id' and wr_is_comment = '1' ";
                    $cmt = sql_fetch($sql);
                    if (isset($cmt)) {
                        if (!($is_admin || ($member['mb_id'] == $cmt['mb_id'] && $cmt['mb_id'])))
                            $cmt['wr_content'] = '';
                        $c_wr_content = $cmt['wr_content'];
                    }
                }
            } ?>
            <span id="edit_<?php echo $comment_id ?>" class="bo_vc_w"></span>
            <span id="reply_<?php echo $comment_id ?>" class="bo_vc_w"></span>
            <input type="hidden" id="secret_comment_<?php echo $comment_id ?>" value="<?php echo strstr($list[$i]['wr_option'], 'secret') ?>">
            <textarea id="save_comment_<?php echo $comment_id ?>" style="display:none"><?php echo get_text($list[$i]['content1'], 0) ?></textarea>
        </div>
        <?php if ($is_cmt_ctrl) { ?>
        <div class="bsk_cmt_actions">
            <button type="button" class="bsk_cmt_opt_btn"><i class="fa fa-ellipsis-v"></i></button>
            <ul class="bsk_cmt_opt_menu">
                <?php if ($list[$i]['is_reply']) { ?><li><a href="<?php echo $c_reply_href ?>" onclick="comment_box('<?php echo $comment_id ?>', 'c'); return false;">답변</a></li><?php } ?>
                <?php if ($list[$i]['is_edit'])  { ?><li><a href="<?php echo $c_edit_href ?>"  onclick="comment_box('<?php echo $comment_id ?>', 'cu'); return false;">수정</a></li><?php } ?>
                <?php if ($list[$i]['is_del'])   { ?><li><a href="<?php echo $list[$i]['del_link'] ?>" onclick="return comment_delete();">삭제</a></li><?php } ?>
            </ul>
        </div>
        <?php } ?>
    </article>
    <?php } ?>
    <?php if ($cmt_amt == 0) { ?><p class="bsk_cmt_empty">등록된 댓글이 없습니다.</p><?php } ?>
</section>

<?php if ($is_comment_write) {
    if ($w == '') $w = 'c'; ?>
<aside id="bo_vc_w" class="bsk_cmt_form_wrap">
    <h2 class="sound_only">댓글쓰기</h2>
    <form name="fviewcomment" id="fviewcomment" action="<?php echo $comment_action_url ?>"
          onsubmit="return fviewcomment_submit(this);" method="post" autocomplete="off">
    <input type="hidden" name="w"          value="<?php echo $w ?>" id="w">
    <input type="hidden" name="bo_table"   value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id"      value="<?php echo $wr_id ?>">
    <input type="hidden" name="comment_id" value="<?php echo $c_id ?>" id="comment_id">
    <input type="hidden" name="sca"        value="<?php echo $sca ?>">
    <input type="hidden" name="sfl"        value="<?php echo $sfl ?>">
    <input type="hidden" name="stx"        value="<?php echo $stx ?>">
    <input type="hidden" name="spt"        value="<?php echo $spt ?>">
    <input type="hidden" name="page"       value="<?php echo $page ?>">
    <input type="hidden" name="is_good"    value="">
    <div class="bsk_cmt_form_body">
        <textarea id="wr_content" name="wr_content" maxlength="10000" required
                  placeholder="댓글을 입력해주세요."
                  <?php if ($comment_min || $comment_max) echo 'onkeyup="check_byte(\'wr_content\',\'char_count\');"' ?>
        ><?php echo $c_wr_content ?></textarea>
        <?php if ($comment_min || $comment_max) { ?>
        <p class="bsk_cmt_charcount"><strong id="char_cnt"><span id="char_count"></span>글자</strong></p>
        <?php } ?>
    </div>
    <div class="bsk_cmt_form_footer">
        <div class="bsk_cmt_form_info">
            <?php if ($is_guest) { ?>
            <input type="text"     name="wr_name"     value="<?php echo get_cookie('ck_sns_name') ?>" required placeholder="이름" class="bsk_input bsk_input_sm">
            <input type="password" name="wr_password" required placeholder="비밀번호" class="bsk_input bsk_input_sm">
            <?php } ?>
            <?php if ($is_guest) echo $captcha_html ?>
        </div>
        <div class="bsk_cmt_form_actions">
            <label class="bsk_cmt_secret_chk">
                <input type="checkbox" name="wr_secret" value="secret" id="wr_secret"> 비밀글
            </label>
            <button type="submit" id="btn_submit" class="bsk_btn bsk_btn_submit">댓글 등록</button>
        </div>
    </div>
    </form>
</aside>
<script>
var save_before = '';
var save_html   = document.getElementById('bo_vc_w').innerHTML;

function fviewcomment_submit(f) {
    f.is_good.value = 0;
    var subject = '', content = '';
    $.ajax({
        url: g5_bbs_url+'/ajax.filter.php', type: 'POST',
        data: { subject: '', content: f.wr_content.value },
        dataType: 'json', async: false, cache: false,
        success: function(data) { subject = data.subject; content = data.content; }
    });
    if (content) { alert("내용에 금지단어('"+content+"')가 포함되어있습니다"); f.wr_content.focus(); return false; }
    var pattern = /(^\s*)|(\s*$)/g;
    document.getElementById('wr_content').value = document.getElementById('wr_content').value.replace(pattern, '');
    if (char_min > 0 || char_max > 0) {
        check_byte('wr_content', 'char_count');
        var cnt = parseInt(document.getElementById('char_count').innerHTML);
        if (char_min > 0 && char_min > cnt) { alert('댓글은 '+char_min+'글자 이상 쓰셔야 합니다.'); return false; }
        else if (char_max > 0 && char_max < cnt) { alert('댓글은 '+char_max+'글자 이하로 쓰셔야 합니다.'); return false; }
    } else if (!document.getElementById('wr_content').value) { alert('댓글을 입력하여 주십시오.'); return false; }
    if (typeof f.wr_name !== 'undefined') { f.wr_name.value = f.wr_name.value.replace(pattern,''); if (!f.wr_name.value) { alert('이름이 입력되지 않았습니다.'); f.wr_name.focus(); return false; } }
    if (typeof f.wr_password !== 'undefined') { f.wr_password.value = f.wr_password.value.replace(pattern,''); if (!f.wr_password.value) { alert('비밀번호가 입력되지 않았습니다.'); f.wr_password.focus(); return false; } }
    <?php if ($is_guest) echo chk_captcha_js(); ?>
    set_comment_token(f);
    document.getElementById('btn_submit').disabled = 'disabled';
    return true;
}

function comment_box(comment_id, work) {
    var el_id, form_el = 'fviewcomment', respond = document.getElementById(form_el);
    el_id = comment_id ? (work == 'c' ? 'reply_'+comment_id : 'edit_'+comment_id) : 'bo_vc_w';
    if (save_before !== el_id) {
        if (save_before) document.getElementById(save_before).style.display = 'none';
        document.getElementById(el_id).style.display = '';
        document.getElementById(el_id).appendChild(respond);
        document.getElementById('wr_content').value = '';
        if (work == 'cu') {
            document.getElementById('wr_content').value = document.getElementById('save_comment_'+comment_id).value;
            if (typeof char_count !== 'undefined') check_byte('wr_content', 'char_count');
            document.getElementById('wr_secret').checked = !!document.getElementById('secret_comment_'+comment_id).value;
        }
        document.getElementById('comment_id').value = comment_id;
        document.getElementById('w').value = work;
        if (save_before) $('#captcha_reload').trigger('click');
        save_before = el_id;
    }
}

function comment_delete() { return confirm('이 댓글을 삭제하시겠습니까?'); }
comment_box('', 'c');
<?php if ($comment_min || $comment_max) { ?>
check_byte('wr_content', 'char_count');
<?php } ?>
<?php if ($board['bo_use_sns'] && ($config['cf_facebook_appid'] || $config['cf_twitter_key'])) { ?>
$(function() {
    $('#bo_vc_send_sns').load('<?php echo G5_SNS_URL ?>/view_comment_write.sns.skin.php?bo_table=<?php echo $bo_table ?>', function() {
        save_html = document.getElementById('bo_vc_w').innerHTML;
    });
});
<?php } ?>
</script>
<?php } ?>

<script>
jQuery(function($) {
    $('.bsk_cmt_toggle').click(function() {
        $(this).toggleClass('bsk_cmt_toggle_open');
        $('#bo_vc').toggle();
    });
    $(document).on('click', '.bsk_cmt_opt_btn', function(e) {
        e.stopPropagation();
        $(this).siblings('.bsk_cmt_opt_menu').toggle();
    });
    $(document).click(function(e) {
        if (!$(e.target).closest('.bsk_cmt_actions').length) $('.bsk_cmt_opt_menu').hide();
    });
});
</script>

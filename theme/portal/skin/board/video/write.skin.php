<?php
if (!defined('_GNUBOARD_')) exit;
include_once(__DIR__ . '/_lib.php');
$video_skin_url = moidam_video_skin_url();
add_stylesheet('<link rel="stylesheet" href="'.$video_skin_url.'/style.css?v='.filemtime(__DIR__.'/style.css').'">', 0);

$_v_row = ($w == 'u' && isset($write['wr_id'])) ? moidam_video_row($bo_table, $write['wr_id']) : array('uid'=>'','duration'=>0,'thumb'=>'','ready'=>0);
$_v_uid = get_text($_v_row['uid']);
$_v_sec = (string) (int) $_v_row['duration'];
$_v_thumb = get_text($_v_row['thumb']);
$_v_ready = $_v_row['ready'] ? '1' : '0';
$_v_thumb_src = moidam_video_thumb_src($_v_thumb);
$_direct = G5_PLUGIN_URL.'/cf_stream/direct_upload.php';
$_status = G5_PLUGIN_URL.'/cf_stream/status.php';
$_v_token = function_exists('cf_stream_token') ? cf_stream_token() : '';
$_wt_mode  = ($w == 'u') ? '글 수정' : '글쓰기';
$_bo_label = get_text($board['bo_subject']);
$_list_url = get_pretty_url($bo_table);
$_content_seed = ($w == 'u' && isset($write['wr_content']) && trim(strip_tags($write['wr_content'])) !== '')
    ? $write['wr_content']
    : (isset($subject) ? $subject : '');
?>
<section id="bsk_write_wrap" class="bsk_video_write_page">
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

    <div class="bsk_write_card">
    <form name="fwrite" id="fwrite" action="<?php echo $action_url ?>" onsubmit="return fwrite_submit(this);"
          method="post" enctype="multipart/form-data" autocomplete="off">
    <?php if ($is_html) { ?><input type="hidden" name="html" value="html1"><?php } ?>
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
    <textarea name="wr_content" id="wr_content" hidden><?php echo htmlspecialchars($_content_seed, ENT_QUOTES); ?></textarea>

    <?php if ($is_name || $is_password) { ?>
    <div class="bsk_write_row">
        <label class="bsk_write_label">작성자</label>
        <div class="bsk_write_field bsk_write_inline">
            <?php if ($is_name) { ?>
            <input type="text" name="wr_name" value="<?php echo $name ?>" id="wr_name" required class="bsk_input bsk_input_sm" placeholder="이름">
            <?php } ?>
            <?php if ($is_password) { ?>
            <input type="password" name="wr_password" id="wr_password" <?php echo $password_required ?> class="bsk_input bsk_input_sm" placeholder="비밀번호">
            <?php } ?>
        </div>
    </div>
    <?php } ?>

    <div class="bsk_write_row bsk_write_row_subject">
        <label for="wr_subject" class="bsk_write_label">제목 <span class="bsk_required">*</span></label>
        <div class="bsk_write_field">
            <input type="text" name="wr_subject" value="<?php echo $subject ?>" id="wr_subject" required
                   class="bsk_input bsk_input_full" placeholder="제목을 입력해주세요" maxlength="255">
        </div>
    </div>

    <div class="bsk_write_row bsk_video_write">
        <label class="bsk_write_label">동영상 <span class="bsk_required">*</span></label>
        <div class="bsk_write_field">
            <input type="hidden" name="cf_uid" id="bsk_cf_uid" value="<?php echo htmlspecialchars($_v_uid, ENT_QUOTES) ?>">
            <input type="hidden" name="cf_duration" id="bsk_cf_duration" value="<?php echo htmlspecialchars($_v_sec, ENT_QUOTES) ?>">
            <input type="hidden" name="cf_thumb" id="bsk_cf_thumb" value="<?php echo htmlspecialchars($_v_thumb, ENT_QUOTES) ?>">
            <input type="hidden" name="cf_ready" id="bsk_cf_ready" value="<?php echo htmlspecialchars($_v_ready, ENT_QUOTES) ?>">
            <div class="bsk_video_uploader" data-direct="<?php echo htmlspecialchars($_direct, ENT_QUOTES) ?>" data-status="<?php echo htmlspecialchars($_status, ENT_QUOTES) ?>" data-token="<?php echo htmlspecialchars($_v_token, ENT_QUOTES) ?>">
                <div class="bsk_video_drop" id="bsk_video_drop">
                    <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                    <p>영상을 여기에 놓거나 파일을 선택하세요</p>
                    <p class="bsk_video_drop_hint">mp4, webm, mov · 서버를 거치지 않고 Cloudflare로 올라갑니다</p>
                    <input type="file" id="bsk_video_file" accept="video/*">
                </div>
                <div class="bsk_video_prog" id="bsk_video_prog" hidden><span class="bsk_video_prog_bar" id="bsk_video_prog_bar"></span><em id="bsk_video_prog_txt">0%</em></div>
                <div class="bsk_video_preview" id="bsk_video_preview"<?php echo $_v_thumb_src ? '' : ' hidden'; ?>>
                    <?php if ($_v_thumb_src) { ?>
                    <img id="bsk_video_preview_img" src="<?php echo htmlspecialchars($_v_thumb_src, ENT_QUOTES) ?>" alt="">
                    <?php } else { ?>
                    <img id="bsk_video_preview_img" alt="" hidden>
                    <?php } ?>
                    <span id="bsk_video_preview_meta"><?php
                        echo $_v_uid ? 'UID '.htmlspecialchars($_v_uid, ENT_QUOTES) : '';
                        echo $_v_ready === '1' ? ' · 준비됨' : ($_v_uid ? ' · 처리중' : '');
                    ?></span>
                </div>
            </div>
        </div>
    </div>

    <?php if ($is_use_captcha) { ?>
    <div class="bsk_write_row">
        <label class="bsk_write_label">보안문자</label>
        <div class="bsk_write_field"><?php echo $captcha_html ?></div>
    </div>
    <?php } ?>

    <div class="bsk_write_submit">
        <a href="<?php echo $_list_url ?>" class="bsk_btn bsk_btn_cancel"><i class="fa fa-list"></i> 목록</a>
        <button type="submit" id="btn_submit" class="bsk_btn bsk_btn_submit">
            <i class="fa fa-check"></i>
            <?php echo ($w == 'u') ? '수정 완료' : '등록하기' ?>
        </button>
    </div>
    </form>
    </div>
</section>
<script>
(function(){
    var drop = document.getElementById('bsk_video_drop');
    var input = document.getElementById('bsk_video_file');
    var bar = document.getElementById('bsk_video_prog_bar');
    var txt = document.getElementById('bsk_video_prog_txt');
    var prog = document.getElementById('bsk_video_prog');
    var preview = document.getElementById('bsk_video_preview');
    var img = document.getElementById('bsk_video_preview_img');
    var meta = document.getElementById('bsk_video_preview_meta');
    var box = document.querySelector('.bsk_video_uploader');
    if (!drop || !input || !box) return;
    var endpoint = box.getAttribute('data-direct') || '';
    var statusUrl = box.getAttribute('data-status') || '';
    var token = box.getAttribute('data-token') || '';
    var maxBasic = 200 * 1024 * 1024;

    function setProg(p){
        prog.hidden = false;
        bar.style.width = p + '%';
        txt.textContent = p + '%';
    }
    function setThumb(src){
        src = String(src || '').trim();
        if (!src) {
            img.removeAttribute('src');
            img.hidden = true;
            return;
        }
        if (/^https?:\/\//i.test(src)) {
            var path = src.replace(/^https?:\/\/[^/]+/i, '');
            if (path && path.charAt(0) === '/') src = path;
        }
        img.hidden = false;
        img.src = src;
        preview.hidden = false;
    }
    function fillMeta(j){
        document.getElementById('bsk_cf_uid').value = j.uid || '';
        document.getElementById('bsk_cf_duration').value = j.duration || 0;
        document.getElementById('bsk_cf_thumb').value = j.thumb || '';
        document.getElementById('bsk_cf_ready').value = j.ready ? '1' : '0';
        setThumb(j.thumb || '');
        meta.textContent = (j.uid ? 'UID ' + j.uid : '') + (j.ready ? ' · 준비됨' : (j.uid ? ' · 처리중' : ''));
        if (j.uid) preview.hidden = false;
    }
    function pollStatus(uid, n){
        n = n || 0;
        var fd = new FormData();
        fd.append('token', token);
        fd.append('bo_table', '<?php echo $bo_table ?>');
        fd.append('uid', uid);
        return fetch(statusUrl, { method:'POST', body: fd, credentials:'same-origin' })
            .then(function(r){ return r.json(); })
            .then(function(j){
                if (j && j.error) throw new Error(j.error);
                fillMeta(j || {});
                if (j && j.ready) return j;
                if (n >= 40) return j;
                return new Promise(function(res){ setTimeout(res, 3000); }).then(function(){ return pollStatus(uid, n+1); });
            });
    }
    function postFile(url, file){
        return new Promise(function(resolve, reject){
            var xhr = new XMLHttpRequest();
            xhr.open('POST', url);
            xhr.upload.onprogress = function(e){
                if (e.lengthComputable) setProg(Math.min(99, Math.round(e.loaded / e.total * 100)));
            };
            xhr.onload = function(){
                if (xhr.status >= 200 && xhr.status < 300) resolve();
                else reject(new Error('Cloudflare 업로드 실패 ('+xhr.status+')'));
            };
            xhr.onerror = function(){ reject(new Error('Cloudflare 업로드 실패')); };
            var f = new FormData();
            f.append('file', file);
            xhr.send(f);
        });
    }
    function upload(file){
        if (!file || file.type.indexOf('video/') !== 0) { alert('영상 파일만 올릴 수 있습니다.'); return; }
        if (file.size > maxBasic) { alert('200MB 이하는 바로 올리고, 더 큰 파일은 아직 지원하지 않습니다.'); return; }
        if (!endpoint || !token) { alert('업로드 경로가 아직 연결되지 않았습니다.'); return; }
        setProg(0);
        setThumb('');
        preview.hidden = true;
        var fd = new FormData();
        fd.append('token', token);
        fd.append('bo_table', '<?php echo $bo_table ?>');
        fd.append('name', file.name || 'moidam-video');
        fetch(endpoint, { method:'POST', body: fd, credentials:'same-origin' })
            .then(function(r){ return r.json().then(function(j){ if (!r.ok) throw new Error(j && j.error ? j.error : 'direct_upload 실패'); return j; }); })
            .then(function(j){
                if (!j.uploadURL || !j.uid) throw new Error(j.error || 'direct_upload 실패');
                document.getElementById('bsk_cf_uid').value = j.uid;
                document.getElementById('bsk_cf_ready').value = '0';
                return postFile(j.uploadURL, file).then(function(){ return j.uid; });
            })
            .then(function(uid){
                setProg(100);
                meta.textContent = 'UID ' + uid + ' · 처리중';
                preview.hidden = false;
                return pollStatus(uid);
            })
            .catch(function(e){ alert(e.message || '업로드에 실패했습니다.'); });
    }
    drop.addEventListener('click', function(){ input.click(); });
    input.addEventListener('change', function(){ if (input.files[0]) upload(input.files[0]); });
    drop.addEventListener('dragover', function(e){ e.preventDefault(); drop.classList.add('is-over'); });
    drop.addEventListener('dragleave', function(){ drop.classList.remove('is-over'); });
    drop.addEventListener('drop', function(e){ e.preventDefault(); drop.classList.remove('is-over'); if (e.dataTransfer.files[0]) upload(e.dataTransfer.files[0]); });

    window.fwrite_submit = function(f){
        var uid = document.getElementById('bsk_cf_uid');
        var ready = document.getElementById('bsk_cf_ready');
        if (!uid || !uid.value) { alert('동영상을 먼저 올려 주세요.'); return false; }
        if (!ready || ready.value !== '1') { alert('영상 처리가 끝날 때까지 기다려 주세요.'); return false; }
        if (f.wr_content && f.wr_subject) f.wr_content.value = f.wr_subject.value || ' ';
        var subject = '', content = '';
        if (window.jQuery) {
            jQuery.ajax({
                url: g5_bbs_url+'/ajax.filter.php', type: 'POST',
                data: { subject: f.wr_subject.value, content: f.wr_content ? f.wr_content.value : '' },
                dataType: 'json', async: false, cache: false,
                success: function(data) { subject = data.subject; content = data.content; }
            });
        }
        if (subject) { alert("제목에 금지단어('"+subject+"')가 포함되어있습니다"); return false; }
        if (content) { alert("내용에 금지단어('"+content+"')가 포함되어있습니다"); return false; }
        <?php if ($is_use_captcha) echo chk_captcha_js(); ?>
        document.getElementById('btn_submit').disabled = 'disabled';
        return true;
    };
})();
</script>

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

$block = '<div class="bsk_write_row bsk_video_write">';
$block .= '<label class="bsk_write_label">동영상 <span class="bsk_required">*</span></label>';
$block .= '<div class="bsk_write_field">';
$block .= '<input type="hidden" name="cf_uid" id="bsk_cf_uid" value="'.htmlspecialchars($_v_uid, ENT_QUOTES).'">';
$block .= '<input type="hidden" name="cf_duration" id="bsk_cf_duration" value="'.htmlspecialchars($_v_sec, ENT_QUOTES).'">';
$block .= '<input type="hidden" name="cf_thumb" id="bsk_cf_thumb" value="'.htmlspecialchars($_v_thumb, ENT_QUOTES).'">';
$block .= '<input type="hidden" name="cf_ready" id="bsk_cf_ready" value="'.htmlspecialchars($_v_ready, ENT_QUOTES).'">';
$block .= '<div class="bsk_video_uploader" data-direct="'.htmlspecialchars($_direct, ENT_QUOTES).'" data-status="'.htmlspecialchars($_status, ENT_QUOTES).'" data-token="'.htmlspecialchars($_v_token, ENT_QUOTES).'">';
$block .= '<div class="bsk_video_drop" id="bsk_video_drop">';
$block .= '<i class="fa fa-cloud-upload" aria-hidden="true"></i>';
$block .= '<p>영상을 여기에 놓거나 파일을 선택하세요</p>';
$block .= '<p class="bsk_video_drop_hint">mp4, webm, mov · 서버를 거치지 않고 Cloudflare로 올라갑니다</p>';
$block .= '<input type="file" id="bsk_video_file" accept="video/*">';
$block .= '</div>';
$block .= '<div class="bsk_video_prog" id="bsk_video_prog" hidden><span class="bsk_video_prog_bar" id="bsk_video_prog_bar"></span><em id="bsk_video_prog_txt">0%</em></div>';
$block .= '<div class="bsk_video_preview" id="bsk_video_preview"'.($_v_thumb_src ? '' : ' hidden').'>';
if ($_v_thumb_src) {
    $block .= '<img src="'.htmlspecialchars($_v_thumb_src, ENT_QUOTES).'" alt="">';
} else {
    $block .= '<img src="" alt="">';
}
$block .= '<span id="bsk_video_preview_meta">'.($_v_uid ? 'UID '.htmlspecialchars($_v_uid, ENT_QUOTES) : '').($_v_ready === '1' ? ' · 준비됨' : ($_v_uid ? ' · 처리중' : '')).'</span>';
$block .= '</div>';
$block .= '</div></div></div>';

ob_start();
include_once G5_THEME_PATH.'/skin/board/_base/write.skin.php';
$html = ob_get_clean();
$html = preg_replace('#<div class="bsk_write_row">\s*<label for="wr_1".*?</div>\s*</div>#s', '', $html, 1);
if (strpos($html, '<div class="bsk_write_row bsk_write_row_content">') !== false) {
    echo str_replace('<div class="bsk_write_row bsk_write_row_content">', $block.'<div class="bsk_write_row bsk_write_row_content">', $html);
} else {
    echo $html.$block;
}
?>
<script>
(function(){
    var drop = document.getElementById('bsk_video_drop');
    var input = document.getElementById('bsk_video_file');
    var bar = document.getElementById('bsk_video_prog_bar');
    var txt = document.getElementById('bsk_video_prog_txt');
    var prog = document.getElementById('bsk_video_prog');
    var preview = document.getElementById('bsk_video_preview');
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
    function fillMeta(j){
        document.getElementById('bsk_cf_uid').value = j.uid || '';
        document.getElementById('bsk_cf_duration').value = j.duration || 0;
        document.getElementById('bsk_cf_thumb').value = j.thumb || '';
        document.getElementById('bsk_cf_ready').value = j.ready ? '1' : '0';
        if (j.thumb) {
            preview.hidden = false;
            preview.querySelector('img').src = String(j.thumb).replace(/^https?:\/\/[^/]+/i, '');
        }
        meta.textContent = (j.uid ? 'UID ' + j.uid : '') + (j.ready ? ' · 준비됨' : ' · 처리중');
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
    var _fwrite = window.fwrite_submit;
    window.fwrite_submit = function(f){
        var uid = document.getElementById('bsk_cf_uid');
        var ready = document.getElementById('bsk_cf_ready');
        if (!uid || !uid.value) { alert('동영상을 먼저 올려 주세요.'); return false; }
        if (!ready || ready.value !== '1') { alert('영상 처리가 끝날 때까지 기다려 주세요.'); return false; }
        return _fwrite ? _fwrite(f) : true;
    };
})();
</script>

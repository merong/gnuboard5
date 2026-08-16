<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 이 에디터가 선택된 게시판은 모바일 글쓰기에도 DHTML을 켭니다.
if (!defined('G5_IS_MOBILE_DHTML_USE')) {
    define('G5_IS_MOBILE_DHTML_USE', true);
}

function page_ai_editor_token()
{
    $token = get_session('ss_page_ai_editor_token');
    if (!$token) {
        $token = function_exists('random_bytes') ? bin2hex(random_bytes(16)) : sha1(uniqid(mt_rand(), true));
        set_session('ss_page_ai_editor_token', $token);
    }
    return $token;
}

function editor_html($id, $content, $is_dhtml_editor = true)
{
    global $config, $w, $board, $write;
    static $js = true;

    if (
        $is_dhtml_editor && $content &&
        (
            (!$w && (isset($board['bo_insert_content']) && !empty($board['bo_insert_content'])))
            || ($w == 'u' && isset($write['wr_option']) && strpos($write['wr_option'], 'html') === false)
        )
    ) {
        if (preg_match('/\r|\n/', $content) && $content === strip_tags($content, '<a><strong><b>')) {
            $content = nl2br($content);
        }
    }

    $editor_url = G5_EDITOR_URL . '/' . $config['cf_editor'];
    $bo_table = isset($board['bo_table']) ? preg_replace('/[^a-z0-9_]/i', '', $board['bo_table']) : '';
    $token = page_ai_editor_token();
    $qs = 'token=' . urlencode($token) . ($bo_table !== '' ? '&bo_table=' . urlencode($bo_table) : '');
    $upload_url = G5_PLUGIN_URL . '/page_ai_editor/upload.php?' . $qs;
    $delete_url = G5_PLUGIN_URL . '/page_ai_editor/delete.php?' . $qs;

    $html = '<span class="sound_only">웹에디터 시작</span>';

    if ($is_dhtml_editor && $js) {
        $css_path = G5_EDITOR_PATH . '/' . $config['cf_editor'] . '/page-ai-editor.css';
        $js_path = G5_EDITOR_PATH . '/' . $config['cf_editor'] . '/page-ai-editor.js';
        $css_v = is_file($css_path) ? filemtime($css_path) : '';
        $js_v = is_file($js_path) ? filemtime($js_path) : '';
        $html .= "\n" . '<link rel="stylesheet" href="' . $editor_url . '/page-ai-editor.css' . ($css_v ? '?v=' . $css_v : '') . '">';
        $html .= "\n" . '<script src="' . $editor_url . '/page-ai-editor.js' . ($js_v ? '?v=' . $js_v : '') . '"></script>';
        $html .= "\n" . '<script>var pageAiEditors = pageAiEditors || {};</script>';
        $js = false;
    }

    if ($is_dhtml_editor) {
        $html .= "\n" . '<textarea id="' . $id . '" name="' . $id . '" class="page-ai-editor-src" maxlength="65536" style="display:none">' . $content . '</textarea>';
        $html .= "\n" . '<div id="page-ai-editor-' . $id . '" class="page-ai-editor-wrap"></div>';
        $html .= "\n<script>\n(function(){\n";
        $html .= "    var el = document.getElementById('page-ai-editor-{$id}');\n";
        $html .= "    var src = document.getElementById('{$id}');\n";
        $html .= "    if (!el || !src || typeof PageAIEditor === 'undefined') return;\n";
        $html .= "    function pageAiSyncByte() {\n";
        $html .= "        if (typeof check_byte === 'function' && document.getElementById('char_count')) {\n";
        $html .= "            check_byte('{$id}', 'char_count');\n";
        $html .= "        }\n";
        $html .= "    }\n";
        $html .= "    pageAiEditors['{$id}'] = PageAIEditor.init(el, {\n";
        $html .= "        format: 'html',\n";
        $html .= "        theme: 'light',\n";
        $html .= "        locale: 'ko',\n";
        $html .= "        contentHeight: { min: 360, max: 720 },\n";
        $html .= "        content: src.value || '<p></p>',\n";
        $html .= "        syncTextarea: src,\n";
        $html .= "        placeholder: '내용을 입력하세요.',\n";
        $html .= "        uploadUrl: " . json_encode($upload_url) . ",\n";
        $html .= "        deleteUrl: " . json_encode($delete_url) . ",\n";
        $html .= "        uploadHeaders: { 'X-Page-AI-Token': " . json_encode($token) . " },\n";
        $html .= "        maxFileSize: 5 * 1024 * 1024,\n";
        $html .= "        allowedMimeTypes: ['image/png', 'image/jpeg', 'image/gif', 'image/webp'],\n";
        $html .= "        toolbar: 'undo redo | fontFamily fontSize | heading list blockquote | bold italic underline strike | alignLeft alignCenter alignRight alignJustify | link image table youtube | search',\n";
        $html .= "        toolbarDesktop: 'undo redo | fontFamily fontSize | heading list | bold italic underline | alignLeft alignCenter alignRight alignJustify | link image table youtube',\n";
        $html .= "        toolbarMobile: 'undo redo | image youtube | bold italic | search',\n";
        $html .= "        toolbarOverflow: 'auto',\n";
        $html .= "        onReady: pageAiSyncByte,\n";
        $html .= "        onChange: pageAiSyncByte\n";
        $html .= "    });\n";
        $html .= "})();\n</script>\n";
    } else {
        $html .= "\n" . '<textarea id="' . $id . '" name="' . $id . '" maxlength="65536" style="width:100%;height:300px">' . $content . '</textarea>';
    }

    $html .= "\n" . '<span class="sound_only">웹 에디터 끝</span>';
    return $html;
}

function get_editor_js($id, $is_dhtml_editor = true)
{
    if ($is_dhtml_editor) {
        return "var {$id}_editor = document.getElementById('{$id}');\n"
            . "try { if (typeof pageAiEditors !== 'undefined' && pageAiEditors['{$id}']) { {$id}_editor.value = pageAiEditors['{$id}'].getHTML(); } } catch (e) {}\n"
            . "var {$id}_editor_data = {$id}_editor ? {$id}_editor.value : '';\n";
    }
    return "var {$id}_editor = document.getElementById('{$id}');\n";
}

function chk_editor_js($id, $is_dhtml_editor = true)
{
    if ($is_dhtml_editor) {
        return "if (!{$id}_editor_data || jQuery.inArray({$id}_editor_data.toLowerCase().replace(/^\\s*|\\s*$/g, ''), ['&nbsp;','<p>&nbsp;</p>','<p><br></p>','<p><br/></p>','<div><br></div>','<p></p>','<br>','<br/>','']) != -1) { alert(\"내용을 입력해 주십시오.\"); return false; }\n";
    }
    return "if (!{$id}_editor.value) { alert(\"내용을 입력해 주십시오.\"); {$id}_editor.focus(); return false; }\n";
}

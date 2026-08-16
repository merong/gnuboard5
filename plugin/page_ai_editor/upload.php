<?php
include_once __DIR__ . '/../../common.php';
include_once __DIR__ . '/page_ai_editor.lib.php';

page_ai_editor_auth();

$content_length = isset($_SERVER['CONTENT_LENGTH']) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
if ($content_length > PAGE_AI_EDITOR_MAX_SIZE + (1024 * 1024) && empty($_FILES)) {
    page_ai_editor_error(413, '파일이 너무 큽니다. (최대 5MB)');
}

if (!isset($_FILES['file']) || is_array($_FILES['file']['name'])) {
    page_ai_editor_error(400, 'file 필드에 파일 하나만 보내 주세요.');
}

$upload = $_FILES['file'];
switch ((int) $upload['error']) {
    case UPLOAD_ERR_OK:
        break;
    case UPLOAD_ERR_INI_SIZE:
    case UPLOAD_ERR_FORM_SIZE:
        page_ai_editor_error(413, '파일이 너무 큽니다. (최대 5MB)');
    case UPLOAD_ERR_NO_FILE:
        page_ai_editor_error(400, '파일이 없습니다.');
    default:
        page_ai_editor_error(500, '업로드에 실패했습니다.');
}

if ((int) $upload['size'] <= 0) {
    page_ai_editor_error(400, '빈 파일입니다.');
}
if ((int) $upload['size'] > PAGE_AI_EDITOR_MAX_SIZE) {
    page_ai_editor_error(413, '파일이 너무 큽니다. (최대 5MB)');
}

$ext = page_ai_editor_ext((string) $upload['name']);
if ($ext === '') {
    page_ai_editor_error(415, '이미지 파일만 올릴 수 있습니다. (png, jpg, gif, webp)');
}

$mime = page_ai_editor_verify_image($upload['tmp_name'], $ext);
if ($mime === false) {
    page_ai_editor_error(415, '이미지 파일만 올릴 수 있습니다. (png, jpg, gif, webp)');
}

$ym = date('ym', G5_SERVER_TIME);
$dir = page_ai_editor_data_root() . '/' . $ym;
if (!is_dir($dir)) {
    @mkdir($dir, G5_DIR_PERMISSION, true);
    @chmod($dir, G5_DIR_PERMISSION);
}
if (!is_dir($dir) || !is_writable($dir)) {
    page_ai_editor_error(500, '업로드 폴더를 만들 수 없습니다.');
}

$stored = (function_exists('random_bytes') ? bin2hex(random_bytes(8)) : uniqid()) . '.' . $ext;
$dest = $dir . '/' . $stored;
if (!move_uploaded_file($upload['tmp_name'], $dest)) {
    page_ai_editor_error(500, '파일을 저장하지 못했습니다.');
}
@chmod($dest, G5_FILE_PERMISSION);

page_ai_editor_json(201, array(
    'url'  => page_ai_editor_public_url($ym . '/' . $stored),
    'name' => get_text(basename((string) $upload['name'])),
    'size' => (int) filesize($dest),
    'mime' => $mime,
));

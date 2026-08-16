<?php
include_once __DIR__ . '/../../common.php';
include_once __DIR__ . '/page_ai_editor.lib.php';

page_ai_editor_auth();

$raw = file_get_contents('php://input');
$body = json_decode($raw === false ? '' : $raw, true);
if (!is_array($body) || !isset($body['url']) || !is_string($body['url']) || trim($body['url']) === '') {
    page_ai_editor_error(400, 'Expected JSON body { "url": "..." }');
}

$full = page_ai_editor_resolve_url($body['url']);
if ($full === null) {
    http_response_code(404);
    exit;
}

if (!@unlink($full)) {
    page_ai_editor_error(500, '파일을 삭제하지 못했습니다.');
}

http_response_code(204);
exit;

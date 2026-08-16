<?php
if (!defined('_GNUBOARD_')) exit;

define('PAGE_AI_EDITOR_MAX_SIZE', 5 * 1024 * 1024);
define('PAGE_AI_EDITOR_ALLOWED', 'jpg|jpeg|png|gif|webp');

function page_ai_editor_json($status, $payload)
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function page_ai_editor_error($status, $message)
{
    page_ai_editor_json($status, array('error' => $message));
}

function page_ai_editor_auth()
{
    global $is_guest, $is_admin, $member, $g5;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        page_ai_editor_error(405, 'Method not allowed');
    }

    $token = isset($_REQUEST['token']) ? (string) $_REQUEST['token'] : '';
    if ($token === '' && isset($_SERVER['HTTP_X_PAGE_AI_TOKEN'])) {
        $token = (string) $_SERVER['HTTP_X_PAGE_AI_TOKEN'];
    }
    $sess = (string) get_session('ss_page_ai_editor_token');
    if ($token === '' || $sess === '' || !hash_equals($sess, $token)) {
        page_ai_editor_error(403, '올바른 방법으로 이용해 주십시오.');
    }

    $bo_table = isset($_REQUEST['bo_table']) ? preg_replace('/[^a-z0-9_]/i', '', (string) $_REQUEST['bo_table']) : '';
    if ($bo_table !== '') {
        $board = get_board_db($bo_table, true);
        if (!$board['bo_table']) {
            page_ai_editor_error(404, '게시판이 없습니다.');
        }
        $write_level = isset($board['bo_write_level']) ? (int) $board['bo_write_level'] : 1;
        $mb_level = isset($member['mb_level']) ? (int) $member['mb_level'] : 1;
        if (!$is_admin && $mb_level < $write_level) {
            page_ai_editor_error(403, '업로드 권한이 없습니다.');
        }
        return $board;
    }

    if ($is_guest) {
        page_ai_editor_error(403, '로그인 후 이용해 주세요.');
    }
    return null;
}

function page_ai_editor_ext($name)
{
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    return preg_match('/^(' . PAGE_AI_EDITOR_ALLOWED . ')$/', $ext) ? $ext : '';
}

function page_ai_editor_verify_image($tmp, $ext)
{
    if (!is_uploaded_file($tmp) && !is_file($tmp)) {
        return false;
    }
    $info = @getimagesize($tmp);
    if (!$info || empty($info['mime'])) {
        return false;
    }
    $map = array(
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
    );
    return (isset($map[$ext]) && $info['mime'] === $map[$ext]) ? $info['mime'] : false;
}

function page_ai_editor_data_root()
{
    return G5_DATA_PATH . '/editor';
}

function page_ai_editor_public_url($relative)
{
    return G5_DATA_URL . '/editor/' . $relative;
}

function page_ai_editor_resolve_url($url)
{
    $url = trim((string) $url);
    if ($url === '') {
        return null;
    }

    $path = parse_url($url, PHP_URL_PATH);
    if (!is_string($path) || $path === '') {
        return null;
    }

    $prefix = parse_url(G5_DATA_URL . '/editor/', PHP_URL_PATH);
    if (!is_string($prefix) || $prefix === '') {
        $prefix = '/' . G5_DATA_DIR . '/editor/';
    }
    if (strpos($path, $prefix) !== 0) {
        return null;
    }

    $relative = substr($path, strlen($prefix));
    if (!preg_match('/^[0-9]{4}\/[a-zA-Z0-9]+\.(' . PAGE_AI_EDITOR_ALLOWED . ')$/', $relative)) {
        return null;
    }

    $root = realpath(page_ai_editor_data_root());
    $full = realpath(page_ai_editor_data_root() . '/' . $relative);
    if ($root === false || $full === false) {
        return null;
    }
    if (strpos($full, $root . DIRECTORY_SEPARATOR) !== 0) {
        return null;
    }
    return $full;
}

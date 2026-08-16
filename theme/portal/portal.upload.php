<?php
/* 포털 테마 파일 업로드 AJAX 엔드포인트 */
ob_start();
$G5_PATH = dirname(dirname(dirname(__FILE__)));
require_once($G5_PATH.'/common.php');
require_once(G5_THEME_PATH.'/portal.settings.php');
ob_get_clean();

header('Content-Type: application/json; charset=utf-8');

if ($is_admin !== 'super') {
    echo json_encode(['ok' => false, 'msg' => '최고관리자만 접근 가능합니다.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'msg' => '잘못된 요청입니다.']);
    exit;
}

$type = trim($_POST['type'] ?? '');

if ($type === 'logo_svg') {
    if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['ok' => false, 'msg' => '파일 업로드에 실패했습니다.']);
        exit;
    }

    $file = $_FILES['file'];

    // 확장자 검사
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'svg') {
        echo json_encode(['ok' => false, 'msg' => 'SVG 파일만 업로드 가능합니다.']);
        exit;
    }

    // 파일 크기 제한 (512 KB)
    if ($file['size'] > 524288) {
        echo json_encode(['ok' => false, 'msg' => '파일 크기는 512KB 이하여야 합니다.']);
        exit;
    }

    $raw = file_get_contents($file['tmp_name']);
    if ($raw === false) {
        echo json_encode(['ok' => false, 'msg' => '파일을 읽을 수 없습니다.']);
        exit;
    }

    // SVG 형식 확인
    $sniff = ltrim($raw);
    if (stripos($sniff, '<svg') === false && stripos($sniff, '<?xml') === false) {
        echo json_encode(['ok' => false, 'msg' => '올바른 SVG 파일이 아닙니다.']);
        exit;
    }

    // 보안: 위험 요소 제거
    $raw = preg_replace('/<script[\s\S]*?<\/script>/i', '', $raw);                 // <script> 블록
    $raw = preg_replace('/\s+on\w+\s*=\s*(["\'])[\s\S]*?\1/i', '', $raw);         // 이벤트 핸들러
    $raw = preg_replace('/<foreignObject[\s\S]*?<\/foreignObject>/i', '', $raw);   // <foreignObject>
    $raw = preg_replace('/href\s*=\s*(["\'])\s*javascript:/i', 'href=$1#', $raw); // javascript: href

    // width/height 미설정 시 viewBox 기반으로 주입 (img 태그에서 렌더링 크기 보장)
    if (!preg_match('/<svg[^>]*\bwidth\s*=/i', $raw) && preg_match('/viewBox\s*=\s*["\'][\d.\s]+\s+([\d.]+)\s+([\d.]+)/i', $raw, $vb)) {
        $vb_w = round((float)$vb[1]);
        $vb_h = round((float)$vb[2]);
        if ($vb_w > 0 && $vb_h > 0) {
            $raw = preg_replace('/<svg\b/i', "<svg width=\"{$vb_w}\" height=\"{$vb_h}\"", $raw, 1);
        }
    }

    // 데이터 디렉터리 생성
    if (!is_dir(PORTAL_DATA_PATH)) {
        @mkdir(PORTAL_DATA_PATH, 0755, true);
    }
    if (!is_writable(PORTAL_DATA_PATH)) {
        echo json_encode(['ok' => false, 'msg' => 'data/ 폴더에 쓰기 권한이 없습니다.']);
        exit;
    }

    $save_path = PORTAL_DATA_PATH . '/logo.svg';
    if (file_put_contents($save_path, $raw) === false) {
        echo json_encode(['ok' => false, 'msg' => '파일 저장에 실패했습니다.']);
        exit;
    }

    $url = G5_THEME_URL . '/data/logo.svg?v=' . time();
    echo json_encode(['ok' => true, 'url' => $url]);
    exit;
}

if ($type === 'top_banner') {
    if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['ok' => false, 'msg' => '파일 업로드에 실패했습니다.']);
        exit;
    }

    $file = $_FILES['file'];

    // 확장자 검사
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed_ext, true)) {
        echo json_encode(['ok' => false, 'msg' => 'jpg, png, gif, webp 파일만 업로드 가능합니다.']);
        exit;
    }

    // 파일 크기 제한 (1MB)
    if ($file['size'] > 1048576) {
        echo json_encode(['ok' => false, 'msg' => '파일 크기는 1MB 이하여야 합니다.']);
        exit;
    }

    // 실제 이미지 파일인지 검증
    $info = @getimagesize($file['tmp_name']);
    if ($info === false) {
        echo json_encode(['ok' => false, 'msg' => '올바른 이미지 파일이 아닙니다.']);
        exit;
    }

    // 데이터 디렉터리 생성
    if (!is_dir(PORTAL_DATA_PATH)) {
        @mkdir(PORTAL_DATA_PATH, 0755, true);
    }
    if (!is_writable(PORTAL_DATA_PATH)) {
        echo json_encode(['ok' => false, 'msg' => 'data/ 폴더에 쓰기 권한이 없습니다.']);
        exit;
    }

    // 이전에 다른 확장자로 저장된 배너 파일 정리
    foreach ($allowed_ext as $old_ext) {
        $old_path = PORTAL_DATA_PATH . '/top_banner.' . $old_ext;
        if (is_file($old_path)) @unlink($old_path);
    }

    $save_path = PORTAL_DATA_PATH . '/top_banner.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $save_path)) {
        echo json_encode(['ok' => false, 'msg' => '파일 저장에 실패했습니다.']);
        exit;
    }

    $url = G5_THEME_URL . '/data/top_banner.' . $ext . '?v=' . time();
    echo json_encode(['ok' => true, 'url' => $url]);
    exit;
}

echo json_encode(['ok' => false, 'msg' => '알 수 없는 업로드 유형입니다.']);
exit;

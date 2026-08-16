<?php
$sub_menu = "200150";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$me_read = isset($_REQUEST['me_read']) ? preg_replace('/[^a-z]/', '', (string) $_REQUEST['me_read']) : '';
if (!in_array($me_read, array('read', 'unread'), true)) {
    $me_read = '';
}

$allowed_sfl = array('me_send', 'me_recv', 'me_memo');
if (!in_array($sfl, $allowed_sfl, true)) {
    $sfl = 'me_memo';
}

$sql_common = " from {$g5['memo_table']} m
    left join {$g5['member_table']} send_mb on send_mb.mb_id = m.me_send_mb_id
    left join {$g5['member_table']} recv_mb on recv_mb.mb_id = m.me_recv_mb_id ";

// 보낸/받은 쌍이 있으면 recv 한 건만 노출. 짝이 없는 send(수신함 삭제분)는 그대로 표시.
$sql_search = " where (
        m.me_type = 'recv'
        or (
            m.me_type = 'send'
            and not exists (
                select 1 from {$g5['memo_table']} pair
                where pair.me_id = m.me_send_id
            )
        )
    ) ";

if ($stx !== '' && $stx !== null) {
    $stx_esc = sql_real_escape_string($stx);
    switch ($sfl) {
        case 'me_send':
            $sql_search .= " and (m.me_send_mb_id like '%{$stx_esc}%' or send_mb.mb_nick like '%{$stx_esc}%' or send_mb.mb_name like '%{$stx_esc}%') ";
            break;
        case 'me_recv':
            $sql_search .= " and (m.me_recv_mb_id like '%{$stx_esc}%' or recv_mb.mb_nick like '%{$stx_esc}%' or recv_mb.mb_name like '%{$stx_esc}%') ";
            break;
        default:
            $sql_search .= " and m.me_memo like '%{$stx_esc}%' ";
            break;
    }
}

if ($me_read === 'read') {
    $sql_search .= " and m.me_read_datetime > '0000-00-00 00:00:00' ";
} elseif ($me_read === 'unread') {
    $sql_search .= " and m.me_read_datetime <= '0000-00-00 00:00:00' ";
}

if (!$sst) {
    $sst = 'm.me_id';
    $sod = 'desc';
}
$allowed_sst = array('m.me_id', 'me_id', 'me_send_datetime', 'me_read_datetime', 'me_send_mb_id', 'me_recv_mb_id');
if ($sst && !in_array($sst, $allowed_sst, true)) {
    $sst = 'm.me_id';
}
if ($sod && !in_array(strtolower($sod), array('asc', 'desc'), true)) {
    $sod = 'desc';
}
$sql_order = " order by {$sst} {$sod} ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql);
$total_count = (int) $row['cnt'];

$sql = " select count(*) as cnt {$sql_common} {$sql_search} and m.me_read_datetime <= '0000-00-00 00:00:00' ";
$row = sql_fetch($sql);
$unread_count = (int) $row['cnt'];

$rows = isset($config['cf_page_rows']) && (int) $config['cf_page_rows'] > 0 ? (int) $config['cf_page_rows'] : 15;
$total_page = $rows > 0 ? (int) ceil($total_count / $rows) : 1;
if ($page < 1) {
    $page = 1;
}
$from_record = ($page - 1) * $rows;

$sql = " select m.*, send_mb.mb_nick as send_nick, send_mb.mb_name as send_name, recv_mb.mb_nick as recv_nick, recv_mb.mb_name as recv_name
            {$sql_common}
            {$sql_search}
            {$sql_order}
            limit {$from_record}, {$rows} ";
$result = sql_query($sql);

if ($me_read) {
    $qstr .= ($qstr ? '&amp;' : '') . 'me_read=' . urlencode($me_read);
}

$listall = '<a href="' . $_SERVER['SCRIPT_NAME'] . '" class="ov_listall">전체목록</a>';

$g5['title'] = '쪽지관리';
require_once './admin.head.php';

$colspan = 8;
?>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">전체 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건 </span></span>
    <span class="btn_ov01"><span class="ov_txt">읽지 않음 </span><span class="ov_num"> <?php echo number_format($unread_count) ?>건 </span></span>
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
    <label for="sfl" class="sound_only">검색대상</label>
    <select name="sfl" id="sfl">
        <option value="me_send"<?php echo get_selected($sfl, 'me_send'); ?>>보낸사람</option>
        <option value="me_recv"<?php echo get_selected($sfl, 'me_recv'); ?>>받는사람</option>
        <option value="me_memo"<?php echo get_selected($sfl, 'me_memo'); ?>>내용</option>
    </select>
    <label for="stx" class="sound_only">검색어</label>
    <input type="text" name="stx" value="<?php echo get_text($stx); ?>" id="stx" class="frm_input" placeholder="아이디, 닉네임, 내용">
    <label for="me_read" class="sound_only">읽음상태</label>
    <select name="me_read" id="me_read">
        <option value=""<?php echo get_selected($me_read, ''); ?>>읽음상태 전체</option>
        <option value="read"<?php echo get_selected($me_read, 'read'); ?>>읽음</option>
        <option value="unread"<?php echo get_selected($me_read, 'unread'); ?>>읽지 않음</option>
    </select>
    <input type="submit" class="btn_submit" value="검색">
</form>

<div class="local_desc01 local_desc">
    <p>보낸쪽지와 받은쪽지가 한 쌍으로 저장된 경우 한 건만 표시합니다. 삭제 시 해당 쌍의 쪽지가 함께 삭제됩니다.</p>
</div>

<form name="fmemolist" id="fmemolist" method="post" action="./memo_delete.php" onsubmit="return fmemolist_submit(this);">
    <input type="hidden" name="sst" value="<?php echo get_text($sst); ?>">
    <input type="hidden" name="sod" value="<?php echo get_text($sod); ?>">
    <input type="hidden" name="sfl" value="<?php echo get_text($sfl); ?>">
    <input type="hidden" name="stx" value="<?php echo get_text($stx); ?>">
    <input type="hidden" name="page" value="<?php echo (int) $page; ?>">
    <input type="hidden" name="me_read" value="<?php echo get_text($me_read); ?>">
    <input type="hidden" name="token" value="">

    <div class="tbl_head01 tbl_wrap">
        <table>
            <caption><?php echo $g5['title']; ?> 목록</caption>
            <thead>
                <tr>
                    <th scope="col">
                        <label for="chkall" class="sound_only">쪽지 전체</label>
                        <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
                    </th>
                    <th scope="col">번호</th>
                    <th scope="col">보낸사람</th>
                    <th scope="col">받는사람</th>
                    <th scope="col">내용 미리보기</th>
                    <th scope="col">보낸시간</th>
                    <th scope="col">읽은시간</th>
                    <th scope="col">관리</th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i = 0; $row = sql_fetch_array($result); $i++) {
                    $num = $total_count - ($page - 1) * $rows - $i;
                    $send_nick = $row['send_nick'] ? get_text($row['send_nick']) : '';
                    $recv_nick = $row['recv_nick'] ? get_text($row['recv_nick']) : '';
                    $send_html = get_text($row['me_send_mb_id']) . ($send_nick ? ' (' . $send_nick . ')' : '');
                    $recv_html = get_text($row['me_recv_mb_id']) . ($recv_nick ? ' (' . $recv_nick . ')' : '');
                    $preview = cut_str(get_text($row['me_memo']), 48);
                    $is_unread = ($row['me_read_datetime'] <= '0000-00-00 00:00:00');
                    $read_html = $is_unread
                        ? '<span class="memo_unread">읽지 않음</span>'
                        : get_text($row['me_read_datetime']);
                    $bg = 'bg' . ($i % 2);
                    $del_href = './memo_delete.php?' . $qstr . '&amp;me_id=' . (int) $row['me_id'];
                    ?>
                    <tr class="<?php echo $bg; ?>">
                        <td class="td_chk">
                            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo $preview; ?> 쪽지</label>
                            <input type="checkbox" name="chk[]" value="<?php echo (int) $row['me_id']; ?>" id="chk_<?php echo $i; ?>">
                        </td>
                        <td class="td_num_c"><?php echo number_format($num); ?></td>
                        <td class="td_left td_mbid"><?php echo $send_html; ?></td>
                        <td class="td_left td_mbid"><?php echo $recv_html; ?></td>
                        <td class="td_left td_memo_preview"><button type="button" class="memo_view_btn" data-me-id="<?php echo (int) $row['me_id']; ?>"><?php echo $preview; ?></button></td>
                        <td class="td_datetime"><?php echo get_text($row['me_send_datetime']); ?></td>
                        <td class="td_datetime"><?php echo $read_html; ?></td>
                        <td class="td_mng td_mng_m">
                            <button type="button" class="btn btn_03 memo_view_btn" data-me-id="<?php echo (int) $row['me_id']; ?>">보기</button>
                            <a href="<?php echo $del_href; ?>" onclick="return delete_confirm(this);" class="btn btn_02">삭제</a>
                        </td>
                    </tr>
                    <?php
                }

                if ($i == 0) {
                    echo '<tr><td colspan="' . $colspan . '" class="empty_table">자료가 없습니다.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="btn_fixed_top">
        <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
    </div>
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'] . '?' . $qstr . '&amp;page='); ?>

<div id="memo_modal" class="popup-overlay" hidden>
    <div class="popup-content memo_modal_box" role="dialog" aria-modal="true" aria-labelledby="memo_modal_title">
        <div class="popup-header">
            <h2 id="memo_modal_title" class="popup-title">쪽지 보기</h2>
            <button type="button" class="popup-close-btn" id="memo_modal_close" aria-label="닫기">×</button>
        </div>
        <div class="popup-body">
            <dl class="memo_modal_meta">
                <div><dt>보낸사람</dt><dd id="mm_send"></dd></div>
                <div><dt>받는사람</dt><dd id="mm_recv"></dd></div>
                <div><dt>보낸시간</dt><dd id="mm_send_time"></dd></div>
                <div><dt>읽은시간</dt><dd id="mm_read_time"></dd></div>
                <div><dt>구분</dt><dd id="mm_type"></dd></div>
            </dl>
            <div id="mm_body" class="memo_modal_body"></div>
        </div>
        <div class="popup-footer">
            <button type="button" class="btn btn_02" id="memo_modal_ok">닫기</button>
        </div>
    </div>
</div>

<script>
function fmemolist_submit(f) {
    if (!is_checked("chk[]")) {
        alert(document.pressed + " 하실 항목을 하나 이상 선택하세요.");
        return false;
    }
    if (document.pressed === "선택삭제") {
        if (!confirm("선택한 쪽지를 삭제하면 보낸/받은 쌍이 함께 삭제되며 복구할 수 없습니다.\n\n정말 삭제하시겠습니까?")) {
            return false;
        }
    }
    return true;
}

(function() {
    var $modal = $("#memo_modal");
    function closeMemoModal() {
        $modal.attr("hidden", true);
        $("body").css("overflow", "");
    }
    function openMemoModal(meId) {
        var token = get_ajax_token();
        if (!token) {
            alert("토큰 정보가 올바르지 않습니다.");
            return;
        }
        $.ajax({
            type: "POST",
            url: "./memo_view.php",
            dataType: "json",
            data: { me_id: meId, token: token },
            success: function(res) {
                if (!res || res.error) {
                    alert((res && res.error) ? res.error : "쪽지를 불러오지 못했습니다.");
                    return;
                }
                $("#mm_send").text(res.send);
                $("#mm_recv").text(res.recv);
                $("#mm_send_time").text(res.send_time);
                $("#mm_read_time").html(res.unread ? '<span class="memo_unread">읽지 않음</span>' : $("<span>").text(res.read_time));
                $("#mm_type").text(res.type);
                $("#mm_body").html(res.body);
                $modal.removeAttr("hidden");
                $("body").css("overflow", "hidden");
                $("#memo_modal_close").trigger("focus");
            },
            error: function() {
                alert("쪽지를 불러오지 못했습니다.");
            }
        });
    }
    $(document).on("click", ".memo_view_btn", function() {
        openMemoModal($(this).data("me-id"));
    });
    $("#memo_modal_close, #memo_modal_ok").on("click", closeMemoModal);
    $modal.on("click", function(e) {
        if (e.target === this) closeMemoModal();
    });
    $(document).on("keydown", function(e) {
        if (e.key === "Escape" && !$modal.is("[hidden]")) closeMemoModal();
    });
})();
</script>

<?php
require_once './admin.tail.php';

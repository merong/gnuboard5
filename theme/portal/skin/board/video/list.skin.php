<?php
if (!defined('_GNUBOARD_')) exit;
include_once(__DIR__ . '/_lib.php');
$video_skin_url = moidam_video_skin_url();
add_stylesheet('<link rel="stylesheet" href="'.$video_skin_url.'/style.css?v='.filemtime(__DIR__.'/style.css').'">', 0);

$list_count = count($list);
$cols = max(2, (int) ($bo_gallery_cols ?: 3));
$sf_items = array();
$sf_customer = moidam_video_customer_code();
$is_mobile_skin = defined('G5_IS_MOBILE') && G5_IS_MOBILE;
$can_classic = !$is_mobile_skin || $is_admin || $is_auth;
?>
<div id="bsk_gall_wrap" class="bsk_video_wrap<?php echo $can_classic ? '' : ' bsk_video_sf_only'; ?>">

<?php if ($is_category) { ?>
<nav class="bsk_cate_nav"><ul><?php echo $category_option ?></ul></nav>
<?php } ?>

<form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL ?>/board_list_update.php"
      onsubmit="return fboardlist_submit(this);" method="post">
<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="spt" value="<?php echo $spt ?>">
<input type="hidden" name="sca" value="<?php echo $sca ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="sw" value="">

<div class="bsk_toolbar">
    <span class="bsk_total">전체 <strong><?php echo number_format($total_count) ?></strong>건</span>
    <div class="bsk_btn_group">
        <?php if ($admin_href) { ?><a href="<?php echo $admin_href ?>" class="bsk_btn bsk_btn_admin"><i class="fa fa-cog"></i></a><?php } ?>
        <button type="button" class="bsk_btn bsk_btn_icon bsk_search_toggle"><i class="fa fa-search"></i></button>
        <?php if ($is_admin == 'super' || $is_auth) { ?>
        <button type="button" class="bsk_btn bsk_btn_icon bsk_admin_opt_toggle"><i class="fa fa-ellipsis-v"></i></button>
        <?php if ($is_checkbox) { ?>
        <ul class="bsk_admin_opt_menu" style="display:none">
            <li><button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value">선택삭제</button></li>
            <li><button type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value">선택복사</button></li>
            <li><button type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value">선택이동</button></li>
        </ul>
        <?php } ?>
        <?php } ?>
        <?php if ($can_classic) { ?>
        <div class="bsk_viewmode" role="group" aria-label="보기 방식">
            <button type="button" class="bsk_viewmode_btn<?php echo $is_mobile_skin ? '' : ' is-on'; ?>" data-mode="list">목록</button>
            <button type="button" class="bsk_viewmode_btn<?php echo $is_mobile_skin ? ' is-on' : ''; ?>" data-mode="short">숏폼으로 보기</button>
        </div>
        <?php } ?>
        <?php if ($write_href) { ?><a href="<?php echo $write_href ?>" class="bsk_btn bsk_btn_write"><i class="fa fa-pencil"></i> 글쓰기</a><?php } ?>
    </div>
</div>

<?php if ($is_checkbox) { ?>
<div class="bsk_gall_allchk">
    <input type="checkbox" id="chkall" onclick="if(this.checked) all_checked(true); else all_checked(false);">
    <label for="chkall">전체선택</label>
</div>
<?php } ?>

<div id="moidam-video-classic"<?php echo $is_mobile_skin ? ' style="display:none"' : ''; ?>>
<ul class="bsk_gall_grid bsk_video_grid bsk_gall_cols_<?php echo $cols ?>">
<?php if ($list_count == 0) { ?>
    <li class="bsk_empty" style="grid-column:1/-1">게시물이 없습니다.</li>
<?php } ?>
<?php for ($i = 0; $i < $list_count; $i++) {
    $item = $list[$i];
    $href = $item['href'];
    $row = moidam_video_row($bo_table, $item['wr_id']);
    $uid = $row['uid'];
    $sec = $row['duration'];
    $thumb_src = moidam_video_thumb_src($row['thumb']);
    $ready = $row['ready'] ? '1' : '0';
    $dur = moidam_video_duration($sec);
    $sf_items[] = array(
        'wr_id' => (int) $item['wr_id'],
        'subject' => get_text(strip_tags(isset($item['wr_subject']) ? $item['wr_subject'] : $item['subject'])),
        'href' => $href,
        'uid' => $uid,
        'duration' => (int) $sec,
        'thumb' => $thumb_src,
        'ready' => $row['ready'] ? 1 : 0,
        'customer' => $sf_customer,
        'name' => get_text(strip_tags(isset($item['wr_name']) ? $item['wr_name'] : $item['name'])),
        'datetime' => isset($item['datetime2']) ? $item['datetime2'] : (isset($item['datetime']) ? $item['datetime'] : ''),
        'hit' => (int) (isset($item['wr_hit']) ? $item['wr_hit'] : 0),
    );
?>
<li class="bsk_gall_item bsk_video_item<?php echo ($wr_id == $item['wr_id']) ? ' bsk_current_item' : '' ?>">
    <?php if ($is_checkbox) { ?>
    <span class="bsk_gall_chk">
        <input type="checkbox" name="chk_wr_id[]" value="<?php echo $item['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
        <label for="chk_wr_id_<?php echo $i ?>"><span class="sound_only"><?php echo $item['subject'] ?></span></label>
    </span>
    <?php } ?>
    <a href="<?php echo $href ?>" class="bsk_gall_link">
        <span class="bsk_gall_thumb bsk_video_thumb">
            <?php if ($item['is_notice']) { ?>
            <span class="bsk_gall_notice_cover"><em class="bsk_badge bsk_badge_notice">공지</em></span>
            <?php } elseif ($thumb_src) { ?>
            <img src="<?php echo htmlspecialchars($thumb_src, ENT_QUOTES) ?>" alt="" loading="lazy">
            <?php } else { ?>
            <span class="bsk_gall_no_img"><i class="fa fa-play-circle"></i></span>
            <?php } ?>
            <span class="bsk_video_play" aria-hidden="true"><i class="fa fa-play"></i></span>
            <?php if ($ready !== '1' && !$item['is_notice']) { ?>
            <span class="bsk_video_proc">처리중</span>
            <?php } elseif ($dur) { ?>
            <span class="bsk_video_dur"><?php echo $dur ?></span>
            <?php } ?>
            <?php if ($item['icon_new']) { ?><span class="bsk_gall_badge_new">N</span><?php } ?>
        </span>
    </a>
    <div class="bsk_gall_info">
        <?php if ($is_category && $item['ca_name']) { ?>
        <span class="bsk_badge bsk_badge_cate"><?php echo $item['ca_name'] ?></span>
        <?php } ?>
        <a href="<?php echo $href ?>" class="bsk_gall_tit_link">
            <span class="bsk_gall_tit"><?php echo $item['subject'] ?>
                <?php echo function_exists('moidam_cmt_badge') ? moidam_cmt_badge($item['wr_comment'] ?? 0) : ''; ?>
            </span>
        </a>
        <span class="bsk_gall_meta">
            <span class="sv_use"><?php echo $item['name'] ?></span>
            <span class="bsk_meta_sep">·</span>
            <span><?php echo $item['datetime2'] ?></span>
            <span class="bsk_meta_sep">·</span>
            <span><i class="fa fa-eye"></i> <?php echo number_format($item['wr_hit']) ?></span>
        </span>
    </div>
</li>
<?php } ?>
</ul>
</div>
<div id="moidam-shortform-root"<?php echo $is_mobile_skin ? '' : ' hidden'; ?>></div>
<script type="application/json" id="moidam-shortform-data"><?php echo json_encode($sf_items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP); ?></script>
<?php
$sf_css = __DIR__ . '/shortform/dist/moidam-shortform.css';
$sf_js = __DIR__ . '/shortform/dist/moidam-shortform.js';
if (is_file($sf_css) && is_file($sf_js)) {
    echo '<link rel="stylesheet" href="'.$video_skin_url.'/shortform/dist/moidam-shortform.css?v='.filemtime($sf_css).'">'."\n";
    echo '<script src="'.$video_skin_url.'/shortform/dist/moidam-shortform.js?v='.filemtime($sf_js).'"></script>'."\n";
}
?>

<div class="bsk_pager"><?php echo $write_pages ?></div>
<?php if ($write_href) { ?>
<div class="bsk_toolbar bsk_toolbar_bottom">
    <div class="bsk_btn_group">
        <a href="<?php echo $write_href ?>" class="bsk_btn bsk_btn_write"><i class="fa fa-pencil"></i> 글쓰기</a>
    </div>
</div>
<?php } ?>
</form>

<div class="bsk_search_wrap" style="display:none">
    <div class="bsk_search_overlay"></div>
    <div class="bsk_search_box">
        <form name="fsearch" method="get">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
        <input type="hidden" name="sca" value="<?php echo $sca ?>">
        <input type="hidden" name="sop" value="and">
        <select name="sfl" class="bsk_select bsk_select_sm"><?php echo get_board_sfl_select_options($sfl) ?></select>
        <div class="bsk_search_input_row">
            <input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" required placeholder="검색어 입력" class="bsk_input bsk_search_input">
            <button type="submit" class="bsk_btn bsk_btn_submit"><i class="fa fa-search"></i></button>
        </div>
        <button type="button" class="bsk_search_close"><i class="fa fa-times"></i> 닫기</button>
        </form>
    </div>
</div>
</div>
<script>
jQuery(function($) {
    $('.bsk_search_toggle').click(function() { $('.bsk_search_wrap').toggle(); });
    $('.bsk_search_overlay, .bsk_search_close').click(function() { $('.bsk_search_wrap').hide(); });
    $('.bsk_admin_opt_toggle').click(function(e) { e.stopPropagation(); $('.bsk_admin_opt_menu').toggle(); });
    $(document).click(function(e) { if (!$(e.target).closest('.bsk_admin_opt_toggle,.bsk_admin_opt_menu').length) $('.bsk_admin_opt_menu').hide(); });

    var KEY = 'moidam-video-view';
    var classic = document.getElementById('moidam-video-classic');
    var root = document.getElementById('moidam-shortform-root');
    var mounted = false;
    var canClassic = <?php echo $can_classic ? 'true' : 'false'; ?>;
    var preferShort = <?php echo $is_mobile_skin ? 'true' : 'false'; ?>;
    function applyView(mode) {
        var isShort = mode === 'short';
        if (isShort && !(window.MoidamShortform && typeof window.MoidamShortform.mount === 'function')) {
            isShort = false;
            mode = 'list';
        }
        $('.bsk_viewmode_btn').removeClass('is-on').filter('[data-mode="' + mode + '"]').addClass('is-on');
        if (classic) classic.style.display = isShort ? 'none' : '';
        if (root) {
            if (isShort) root.removeAttribute('hidden');
            else root.setAttribute('hidden', 'hidden');
        }
        if (isShort && !mounted && root && window.MoidamShortform && typeof window.MoidamShortform.mount === 'function') {
            var dataEl = document.getElementById('moidam-shortform-data');
            var items = [];
            try { items = JSON.parse((dataEl && dataEl.textContent) || '[]'); } catch (err) { items = []; }
            window.MoidamShortform.mount(root, { items: items, startIndex: 0 });
            mounted = true;
        }
        try { sessionStorage.setItem(KEY, isShort ? 'short' : 'list'); } catch (err) {}
    }
    $('.bsk_viewmode_btn').on('click', function() { applyView($(this).attr('data-mode')); });
    if (!canClassic || preferShort) {
        var saved = 'short';
        if (canClassic) {
            try { saved = sessionStorage.getItem(KEY) || 'short'; } catch (err) {}
        }
        applyView(saved === 'list' && canClassic ? 'list' : 'short');
    } else {
        var saved = 'list';
        try { saved = sessionStorage.getItem(KEY) || 'list'; } catch (err) {}
        if (saved === 'short') applyView('short');
    }
});
</script>
<?php if ($is_checkbox) { ?>
<script>
function all_checked(sw) { var f = document.fboardlist; for (var i=0; i<f.length; i++) { if (f.elements[i].name=='chk_wr_id[]') f.elements[i].checked=sw; } }
function fboardlist_submit(f) {
    var chk=0; for (var i=0; i<f.length; i++) { if (f.elements[i].name=='chk_wr_id[]' && f.elements[i].checked) chk++; }
    if (!chk) { alert(document.pressed+'할 게시물을 하나 이상 선택하세요.'); return false; }
    if (document.pressed=='선택복사') { select_copy('copy'); return; }
    if (document.pressed=='선택이동') { select_copy('move'); return; }
    if (document.pressed=='선택삭제') { if (!confirm('선택한 게시물을 정말 삭제하시겠습니까?')) return false; f.removeAttribute('target'); f.action=g5_bbs_url+'/board_list_update.php'; }
    return true;
}
function select_copy(sw) { var f=document.fboardlist; window.open('','move','left=50,top=50,width=500,height=550,scrollbars=1'); f.sw.value=sw; f.target='move'; f.action=g5_bbs_url+'/move.php'; f.submit(); }
</script>
<?php } ?>

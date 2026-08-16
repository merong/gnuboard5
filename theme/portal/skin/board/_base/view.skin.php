<?php if (!defined('_GNUBOARD_')) exit;
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
?>
<script src="<?php echo G5_JS_URL ?>/viewimageresize.js"></script>

<article id="bsk_view_wrap">

    <!-- 제목 -->
    <header class="bsk_view_header">
        <?php if ($category_name) { ?>
        <a href="<?php echo $list[$i]['ca_name_href'] ?? '' ?>" class="bsk_badge bsk_badge_cate"><?php echo $view['ca_name'] ?></a>
        <?php } ?>
        <h2 class="bsk_view_tit"><?php echo get_text($view['wr_subject']) ?></h2>
    </header>

    <!-- 메타 정보 -->
    <div class="bsk_view_info">
        <div class="bsk_view_author">
            <span class="bsk_view_avatar"><?php echo get_member_profile_img($view['mb_id']) ?></span>
            <span class="sv_use bsk_view_name"><?php echo $view['name'] ?></span>
            <?php if ($is_ip_view) { ?><span class="bsk_view_ip">(<?php echo $ip ?>)</span><?php } ?>
        </div>
        <div class="bsk_view_stats">
            <span class="bsk_view_stat"><i class="fa fa-clock-o"></i> <?php echo date('Y-m-d H:i', strtotime($view['wr_datetime'])) ?></span>
            <span class="bsk_view_stat"><i class="fa fa-eye"></i> <?php echo number_format($view['wr_hit']) ?></span>
            <span class="bsk_view_stat"><a href="#bo_vc"><i class="fa fa-comment-o"></i> <?php echo number_format($view['wr_comment']) ?></a></span>
        </div>
    </div>

    <!-- 액션 버튼 -->
    <div class="bsk_view_actions">
        <div class="bsk_btn_group">
            <a href="<?php echo $list_href ?>" class="bsk_btn bsk_btn_icon" title="목록"><i class="fa fa-list"></i> 목록</a>
            <?php if ($reply_href) { ?><a href="<?php echo $reply_href ?>" class="bsk_btn bsk_btn_icon" title="답변"><i class="fa fa-reply"></i> 답변</a><?php } ?>
            <?php if ($write_href) { ?><a href="<?php echo $write_href ?>" class="bsk_btn bsk_btn_write"><i class="fa fa-pencil"></i> 글쓰기</a><?php } ?>
        </div>
        <?php if ($update_href || $delete_href || $copy_href || $move_href) { ?>
        <div class="bsk_btn_group">
            <?php if ($update_href) { ?><a href="<?php echo $update_href ?>" class="bsk_btn">수정</a><?php } ?>
            <?php if ($delete_href) { ?><a href="<?php echo $delete_href ?>" class="bsk_btn bsk_btn_danger" onclick="del(this.href); return false;">삭제</a><?php } ?>
            <?php if ($copy_href) { ?><a href="<?php echo $copy_href ?>" class="bsk_btn" onclick="board_move(this.href); return false;">복사</a><?php } ?>
            <?php if ($move_href) { ?><a href="<?php echo $move_href ?>" class="bsk_btn" onclick="board_move(this.href); return false;">이동</a><?php } ?>
        </div>
        <?php } ?>
    </div>

    <!-- 본문 -->
    <section class="bsk_view_body">
        <?php
        if (count($view['file'])) {
            echo '<div id="bo_v_img">';
            foreach ($view['file'] as $vf) echo get_file_thumbnail($vf);
            echo '</div>';
        }
        ?>
        <div class="bsk_view_content" id="bo_v_con"><?php echo get_view_thumbnail($view['content']) ?></div>
        <?php if ($is_signature) { ?><p class="bsk_view_sig"><?php echo $signature ?></p><?php } ?>
    </section>

    <!-- SNS 공유 -->
    <?php
    $_share_protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $_share_url  = $_share_protocol . '://' . $_SERVER['HTTP_HOST'] . '/bbs/board.php?bo_table=' . urlencode($bo_table) . '&wr_id=' . (int)$wr_id;
    $_share_title = get_text($view['wr_subject']);
    $_share_img  = '';
    foreach ($view['file'] as $_sf) {
        if (!empty($_sf['view']) && !empty($_sf['href'])) { $_share_img = $_sf['href']; break; }
    }
    $_kakao_key = trim($config['cf_kakao_js_apikey'] ?? '');
    $_enc_url   = rawurlencode($_share_url);
    $_enc_title = rawurlencode($_share_title);
    $_enc_img   = rawurlencode($_share_img);
    ?>
    <div class="bsk_share">
        <span class="bsk_share_label">공유하기</span>
        <div class="bsk_share_btns">
            <?php if ($_kakao_key): ?>
            <button type="button" class="bsk_share_btn bsk_share_kakao" onclick="shareKakao()" title="카카오톡 공유">
                <svg class="bsk_share_icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3C6.477 3 2 6.477 2 10.5c0 2.611 1.584 4.9 3.984 6.306L5 21l4.207-2.297C10.078 18.9 11.024 19 12 19c5.523 0 10-3.477 10-7.5S17.523 3 12 3z"/></svg>
                카카오톡
            </button>
            <?php endif; ?>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $_enc_url ?>"
               class="bsk_share_btn bsk_share_fb" target="_blank" rel="noopener" title="페이스북 공유" onclick="return popShare(this.href)">
                <i class="fa fa-facebook" aria-hidden="true"></i> Facebook
            </a>
            <a href="https://x.com/intent/tweet?url=<?php echo $_enc_url ?>&text=<?php echo $_enc_title ?>"
               class="bsk_share_btn bsk_share_x" target="_blank" rel="noopener" title="X 공유" onclick="return popShare(this.href)">
                <svg class="bsk_share_icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                X
            </a>
            <a href="https://share.naver.com/web/shareView?url=<?php echo $_enc_url ?>&title=<?php echo $_enc_title ?>"
               class="bsk_share_btn bsk_share_naver" target="_blank" rel="noopener" title="네이버 공유" onclick="return popShare(this.href)">
                <svg class="bsk_share_icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M16.273 12.845L7.376 0H0v24h7.727V11.155L16.624 24H24V0h-7.727z"/></svg>
                네이버
            </a>
            <?php if ($_share_img): ?>
            <a href="https://pinterest.com/pin/create/button/?url=<?php echo $_enc_url ?>&media=<?php echo $_enc_img ?>&description=<?php echo $_enc_title ?>"
               class="bsk_share_btn bsk_share_pin" target="_blank" rel="noopener" title="핀터레스트 공유" onclick="return popShare(this.href)">
                <i class="fa fa-pinterest-p" aria-hidden="true"></i> Pinterest
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- 추천/비추천 -->
    <?php if ($good_href || $nogood_href) { ?>
    <div class="bsk_view_react">
        <?php if ($good_href) { ?>
        <a href="<?php echo $good_href.'&amp;'.$qstr ?>" id="good_button" class="bsk_react_btn bsk_react_good">
            <i class="fa fa-thumbs-o-up"></i> 추천 <strong><?php echo number_format($view['wr_good']) ?></strong>
        </a>
        <b id="bo_v_act_good" class="bsk_react_msg"></b>
        <?php } ?>
        <?php if ($nogood_href) { ?>
        <a href="<?php echo $nogood_href.'&amp;'.$qstr ?>" id="nogood_button" class="bsk_react_btn bsk_react_nogood">
            <i class="fa fa-thumbs-o-down"></i> 비추천 <strong><?php echo number_format($view['wr_nogood']) ?></strong>
        </a>
        <b id="bo_v_act_nogood" class="bsk_react_msg"></b>
        <?php } ?>
    </div>
    <?php } ?>

    <!-- 첨부파일 -->
    <?php
    $dl_cnt = 0;
    foreach ($view['file'] as $vf) {
        if (isset($vf['source']) && $vf['source'] && !$vf['view']) $dl_cnt++;
    }
    ?>
    <?php if ($dl_cnt) { ?>
    <section class="bsk_view_files">
        <h3>첨부파일</h3>
        <ul>
        <?php foreach ($view['file'] as $vf) {
            if (!isset($vf['source']) || !$vf['source'] || $vf['view']) continue; ?>
            <li>
                <a href="<?php echo $vf['href'] ?>" class="view_file_download bsk_file_link">
                    <i class="fa fa-folder-open"></i>
                    <strong><?php echo $vf['source'] ?></strong>
                    <span class="bsk_file_size"><?php echo $vf['content'] ?> (<?php echo $vf['size'] ?>)</span>
                </a>
                <span class="bsk_file_info"><?php echo $vf['download'] ?>회 다운로드</span>
            </li>
        <?php } ?>
        </ul>
    </section>
    <?php } ?>

    <!-- 관련 링크 -->
    <?php if (isset($view['link']) && array_filter($view['link'])) { ?>
    <section class="bsk_view_links">
        <h3>관련 링크</h3>
        <ul>
        <?php for ($i = 1; $i <= count($view['link']); $i++) {
            if (!$view['link'][$i]) continue; ?>
            <li>
                <a href="<?php echo $view['link_href'][$i] ?>" target="_blank" class="bsk_link_item">
                    <i class="fa fa-external-link"></i> <?php echo cut_str($view['link'][$i], 70) ?>
                </a>
            </li>
        <?php } ?>
        </ul>
    </section>
    <?php } ?>

    <!-- 이전글 / 다음글 -->
    <?php if ($prev_href || $next_href) { ?>
    <nav class="bsk_view_nav">
        <?php if ($next_href) { ?>
        <a href="<?php echo $next_href ?>" class="bsk_nav_item bsk_nav_next">
            <span class="bsk_nav_label"><i class="fa fa-chevron-up"></i> 다음글</span>
            <span class="bsk_nav_tit"><?php echo $next_wr_subject ?></span>
        </a>
        <?php } ?>
        <?php if ($prev_href) { ?>
        <a href="<?php echo $prev_href ?>" class="bsk_nav_item bsk_nav_prev">
            <span class="bsk_nav_label"><i class="fa fa-chevron-down"></i> 이전글</span>
            <span class="bsk_nav_tit"><?php echo $prev_wr_subject ?></span>
        </a>
        <?php } ?>
    </nav>
    <?php } ?>

    <!-- 댓글 -->
    <?php include_once(G5_BBS_PATH.'/view_comment.php') ?>

</article>

<?php if ($_kakao_key): ?>
<script src="https://t1.kakaocdn.net/kakaojs/V1/kakao.min.js" crossorigin="anonymous"></script>
<script>
(function() {
    if (window.Kakao && !Kakao.isInitialized()) Kakao.init('<?php echo addslashes($_kakao_key) ?>');
})();
function shareKakao() {
    if (!window.Kakao || !Kakao.isInitialized()) { alert('카카오 SDK가 초기화되지 않았습니다.'); return; }
    var opts = {
        objectType: 'feed',
        content: {
            title: <?php echo json_encode($_share_title) ?>,
            <?php if ($_share_img): ?>imageUrl: <?php echo json_encode($_share_img) ?>,<?php endif; ?>
            link: { mobileWebUrl: <?php echo json_encode($_share_url) ?>, webUrl: <?php echo json_encode($_share_url) ?> }
        }
    };
    Kakao.Share.sendDefault(opts);
}
</script>
<?php endif; ?>

<script>
function popShare(url) {
    window.open(url, 'share', 'width=600,height=500,scrollbars=yes,resizable=yes');
    return false;
}

$(function() {
    $('#bo_v_con').viewimageresize();

    $('#good_button, #nogood_button').click(function() {
        var $tx = this.id === 'good_button' ? $('#bo_v_act_good') : $('#bo_v_act_nogood');
        excute_good(this.href, $(this), $tx);
        return false;
    });

    $('a.view_image').click(function() {
        window.open(this.href, 'large_image', 'left=50,top=50,width=10,height=10,resizable=yes,scrollbars=no');
        return false;
    });
});

function excute_good(href, $el, $tx) {
    $.post(href, {js:'on'}, function(data) {
        if (data.error) { alert(data.error); return; }
        if (data.count) {
            $el.find('strong').text(number_format(String(data.count)));
            $tx.text($tx.attr('id').indexOf('nogood') > -1 ? '이 글을 비추천하셨습니다.' : '이 글을 추천하셨습니다.');
            $tx.fadeIn(200).delay(2500).fadeOut(200);
        }
    }, 'json');
}

function board_move(href) {
    window.open(href, 'boardmove', 'left=50,top=50,width=500,height=550,scrollbars=1');
}

<?php if ($board['bo_download_point'] < 0) { ?>
$(function() {
    $('a.view_file_download').click(function() {
        if (!g5_is_member) { alert('다운로드 권한이 없습니다.'); return false; }
        if (!confirm('파일을 다운로드 하시면 포인트가 차감(<?php echo number_format($board['bo_download_point']) ?>점)됩니다.\n\n다운로드 하시겠습니까?')) return false;
        $(this).attr('href', $(this).attr('href') + '&js=on');
        return true;
    });
});
<?php } ?>
</script>

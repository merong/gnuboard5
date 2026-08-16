<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

/*
 * 마루밑다락방 포털 테마 - 모바일 내용관리 스킨
 * ID가 'company'일 경우 테마의 about.php 레이아웃을 최우선으로 가져옵니다.
 */

if ($co_id == 'company') {
    // 테마 루트의 about.php를 인클루드하여 반응형 레이아웃을 그대로 사용합니다.
    include_once(G5_THEME_PATH.'/about.php');
    return; 
}

// 이용 가이드 (guide.php) 연결
if ($co_id == 'guide') {
    include_once(G5_THEME_PATH.'/guide.php');
    return; // 아래 기본 스킨 출력을 중단합니다.
}
?>

<!-- 일반 모바일 내용관리 페이지 스타일 (이용약관 등) -->
<style>
    #ctt { background: var(--portal-surface); padding:20px 15px; line-height:1.7; word-break:break-all; }

    /* 모바일 상단 헤더 영역 */
    #ctt_header { position: relative; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--portal-border); }
    #ctt_title { font-size: 20px; font-weight: 800; color: var(--portal-text); margin: 0; letter-spacing: -1px; }

    /* 모바일 관리 버튼 (우측 상단 배치) */
    #ctt_admin { position: absolute; right: 0; top: 0; }
    #ctt_admin a { display: inline-block; width: 32px; height: 32px; line-height: 32px; text-align: center; background: var(--portal-surface-alt); border: 1px solid var(--portal-border); color: var(--portal-text-sub); border-radius: 4px; font-size: 14px; }

    /* 본문 텍스트 최적화 */
    #ctt_con { font-size: 15px; color: var(--portal-text); }
    #ctt_con img { max-width: 100% !important; height: auto !important; border-radius: 5px; margin: 10px 0; }
    #ctt_con p { margin-bottom: 15px; }

    /* 하단 버튼 영역 */
    .ctt_ft { margin-top: 30px; text-align: center; }
    .btn_b01 { display: inline-block; padding: 10px 20px; background: var(--portal-primary); color: #fff; border-radius: 4px; font-weight: bold; text-decoration: none; }
</style>

<article id="ctt" class="ctt_<?php echo $co_id; ?>">
    <header id="ctt_header">
        <h1 id="ctt_title"><?php echo $g5['title']; ?></h1>
        
        <?php if ($is_admin) { ?>
        <div id="ctt_admin">
            <a href="<?php echo G5_ADMIN_URL; ?>/contentform.php?w=u&amp;co_id=<?php echo $co_id; ?>" title="내용 수정">
                <i class="fa fa-cog" aria-hidden="true"></i>
            </a>
        </div>
        <?php } ?>
    </header>

    <div id="ctt_con">
        <?php echo conv_content($co['co_content'], 1); ?>
    </div>

    <div class="ctt_ft">
        <a href="<?php echo G5_URL; ?>" class="btn_b01">홈으로 돌아가기</a>
    </div>
</article>

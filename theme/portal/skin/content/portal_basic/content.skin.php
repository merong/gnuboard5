<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

/*
 * 마루밑다락방 포털 테마 - 내용관리 스킨 스위처
 * ID가 'company'일 경우 테마의 about.php 레이아웃을 가져옵니다.
 */

if ($co_id == 'company') {
    include_once(G5_THEME_PATH.'/about.php');
    return; // 아래 기본 스킨 출력을 중단합니다.
}

// 이용 가이드 (guide.php) 연결
if ($co_id == 'guide') {
    include_once(G5_THEME_PATH.'/guide.php');
    return; // 아래 기본 스킨 출력을 중단합니다.
}
?>

<!-- 일반 내용관리 페이지 스타일 (이용약관, 개인정보처리방침 등) -->
<style>
    #ctt { background: var(--portal-surface); padding:40px; border:1px solid var(--portal-border); border-radius:8px; margin-bottom:20px; line-height:1.8; position: relative; }

    /* 제목 영역을 상대 좌표로 설정하여 관리 버튼의 기준점으로 사용 */
    #ctt_header { position: relative; margin-bottom: 30px; padding-bottom: 15px; border-bottom: 2px solid var(--portal-primary); display: flex; justify-content: space-between; align-items: flex-end; }

    #ctt_title { font-size: 24px; font-weight: 800; color: var(--portal-text); margin: 0; }

    /* 관리자 버튼(톱니바퀴 등)을 우측으로 강제 이동 */
    #ctt_admin { position: absolute; right: 0; top: 0; display: flex; gap: 5px; }
    #ctt_admin a { display: inline-block; width: 30px; height: 30px; line-height: 30px; text-align: center; background: var(--portal-surface-alt); border: 1px solid var(--portal-border); color: var(--portal-text-sub); border-radius: 3px; font-size: 14px; transition: all 0.2s; }
    #ctt_admin a:hover { background: var(--portal-primary); color: #fff; border-color: var(--portal-primary); }

    #ctt_con { font-size:15px; color: var(--portal-text); clear: both; }
    #ctt_con img { max-width:100% !important; height:auto !important; }
    
    @media (max-width: 768px) {
        #ctt { padding:20px; border:none; }
        #ctt_title { font-size:20px; }
        #ctt_admin { position: static; margin-bottom: 10px; justify-content: flex-end; }
    }
</style>

<article id="ctt" class="ctt_<?php echo $co_id; ?>">
    <header id="ctt_header">
        <h1 id="ctt_title"><?php echo $g5['title']; ?></h1>
        
        <?php if ($is_admin) { ?>
        <div id="ctt_admin">
            <a href="<?php echo G5_ADMIN_URL; ?>/contentform.php?w=u&amp;co_id=<?php echo $co_id; ?>" title="내용 수정"><i class="fa fa-cog" aria-hidden="true"></i></a>
        </div>
        <?php } ?>
    </header>

    <div id="ctt_con">
        <?php echo conv_content($co['co_content'], 1); ?>
    </div>
</article>

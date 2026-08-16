# theme/portal 작업 메모

이 문서는 `theme/portal` 테마에 최근 추가된 기능들의 구조를 정리합니다. `gnuboard5_portal_theme.md`는 테마 초기 제작 시점의 스냅샷(레거시 참고용)이라 현재 코드와 다를 수 있으니, 현재 동작을 파악할 때는 이 문서와 실제 코드를 우선 참고하세요.

## 설정 저장 구조

- 모든 테마 설정은 `data/portal.settings.json` 파일 하나에 저장됩니다.
- `portal.settings.php`의 `portal_get_settings()`가 기본값(`$defaults`)과 저장된 JSON을 병합해 `$portal_settings` 전역 배열을 만듭니다. 새 설정 항목을 추가할 때는 반드시 `$defaults`에도 키를 추가해야 기존 설치본에서 누락 오류가 나지 않습니다.
- 저장은 `portal.save.php` (AJAX, 최고관리자 전용)가 담당하며, 배열/객체 형태의 값은 프론트에서 `base64(JSON.stringify(...))`로 인코딩해 전송합니다 (WAF 우회 + 한글 포함 목적, `_b64enc()` 참고).
- 회원 아이디 배열처럼 콤마 목록으로 입력받는 값은 `portal_save.php`의 `portal_sanitize_id_list()`로 정제합니다(영문/숫자/언더바만 허용).

## 관리자 편집 UI (테마 편집 모달)

- 홈(`index.php`)에서 `$is_admin == 'super'`일 때만 `#portal_editor_overlay` 모달이 렌더링됩니다. 다른 페이지에는 이 모달이 없으므로, 헤더/사이드바 등 전역 요소에서 "설정하러 가기" 버튼을 넣을 땐 모달이 index.php에만 존재한다는 점을 감안해야 합니다.
- 탭 구성: 로고 / 배너(메인 슬라이더) / 헤더배너 / 푸터 / 위젯 / 광고 / 색상.
- 이미지 업로드는 `portal.upload.php` (AJAX, 최고관리자 전용)가 `type` 파라미터로 분기 처리합니다. 현재 지원 타입:
  - `logo_svg`: SVG만 허용, 위험 태그 제거 후 `data/logo.svg`에 고정 저장.
  - `top_banner`: jpg/png/gif/webp, 최대 1MB, `getimagesize()`로 실제 이미지 검증, `data/top_banner.<ext>`로 저장(확장자 바뀌면 이전 파일 정리).
  - 새 업로드 타입을 추가할 때는 이 패턴(확장자 화이트리스트 → 크기 제한 → 내용 검증 → 고정 파일명 저장 → `?v=timestamp` 캐시버스팅)을 따르세요.

## 레이아웃 구조 (head.php)

`#portal_wrap` 안에서 위에서부터:
1. `#portal_top_ad` — 최상단 광고 스크립트 (`top_ad_script`, 비어있으면 미출력)
2. `#portal_top_header` — Header 위 유틸리티 바
   - 좌측: 홈 링크, `G5_USE_SHOP`이 true면(영카트 활성화) 쇼핑몰 링크
   - 우측: 로그인 시 로그아웃/정보수정, 비로그인 시 로그인/회원가입, 관리자면 관리자 링크
3. `#portal_header` — 로고 / 검색창 / `.header_banner`(헤더 배너, 120×60, 있을 때만 렌더링)
4. `#portal_gnb` — 메인 내비게이션

과거엔 검색창 옆에 `.top_menu`(로그아웃/정보수정/관리자, 이후 로그인/회원가입까지)가 있었지만 전부 `#portal_top_header`로 이관되어 **`.top_menu`는 완전히 제거**되었습니다. 검색창 옆 자리는 현재 `.header_banner` 전용입니다.

### 레이아웃 주의사항
`#portal_header .inner`는 `justify-content: space-between`을 쓰지 않습니다(로고+검색창만 있을 때 검색창이 우측 끝까지 밀리는 버그가 있었음). 대신 `.header_banner { margin-left: auto; }`로 배너가 있을 때만 우측에 붙도록 처리합니다. 헤더에 새 요소를 우측 정렬로 추가할 땐 `justify-content`를 되돌리지 말고 `margin-left: auto` 패턴을 따르세요. 단, 모바일(≤768px) 미디어쿼리에는 로고/햄버거 버튼을 양 끝에 배치하기 위해 `justify-content: space-between`이 별도로 남아 있습니다.

## 헤더 배너 (top_banner)

- 설정 키: `top_banner = { img, link, target, alt, exclude_members }`
- 표시 위치: `#portal_header .header_banner`, 120×60px 고정, `object-fit: cover`.
- 모바일(≤768px)에서는 숨김.
- `exclude_members`에 등록된 `mb_id`로 로그인한 회원에게는 배너가 보이지 않습니다.

## 회원별 노출 제외 패턴 (광고 · 헤더 배너)

`portal.settings.php`에 공용 헬퍼가 있습니다:

```php
portal_member_excluded($exclude_array)   // 현재 로그인 회원의 mb_id가 목록에 있으면 true
portal_ad_allowed()                       // ad_exclude_members 기준
portal_top_banner_allowed()               // top_banner.exclude_members 기준
```

- `top_ad_script`(최상단 광고)와 `board_sidebar_ad`(게시판 사이드바 광고)는 공통으로 `ad_exclude_members` 목록을 사용합니다.
- `top_banner`는 자체 `exclude_members` 필드를 가집니다(광고와 별개 목록).
- 앞으로 "특정 회원에게만 안 보이게" 하는 기능을 추가할 때는 새 설정 값에 `exclude_members` 배열을 두고 `portal_member_excluded()`를 재사용하면 됩니다. 관리자 UI 쪽에는 콤마 구분 입력창 + 저장 시 `split(',').map(trim).filter(Boolean)` 패턴을 그대로 복사해 쓰면 됩니다(광고 탭의 `pe_ad_exclude_members`, 헤더배너 탭의 `pe_topbanner_exclude_members` 참고).

## 테마 포인트 컬러 프리셋 (색상 탭)

`css/default.css`는 원래 CSS 커스텀 프로퍼티를 전혀 쓰지 않고 포인트 컬러(`#03c75a`)와 그 hover
색(`#02b050`)을 파일 전체에 하드코딩하고 있었다. 이 기능을 넣으면서 파일 최상단 `:root`에
`--portal-primary`/`--portal-primary-hover` 두 변수를 선언하고, 기존에 하드코딩돼 있던 모든
`#03c75a`/`#02b050`을 `var(--portal-primary)`/`var(--portal-primary-hover)`로 일괄 치환했다
(겉보기 색은 원래와 100% 동일 — 오버라이드 가능하게만 바꾼 것). `index.php`의 관리자 모달 안
인라인 스타일/JS 몇 곳에도 같은 색이 하드코딩돼 있었어서 함께 치환했다. **앞으로 이 브랜드
그린 색을 새로 쓸 일이 생기면 hex를 직접 적지 말고 반드시 `var(--portal-primary)`를 쓸 것**
(안 그러면 프리셋을 바꿔도 그 부분만 원래 그린으로 남는다). 경고/삭제용 빨간색(`#e74c3c` 등)처럼
브랜드 색과 무관한 색상은 그대로 둔다.

**주의**: 최초 치환 때 정확히 `#03c75a`/`#02b050` 문자열만 찾아 바꿨는데, 실제로는 `:hover`
상태에 손으로 고른 살짝 다른 초록(`#02b350`, `#02a34c` 등, `search_wrap button:hover`,
`.ol_btn_login:hover`, `.guide_bar_btn:hover`)이 하드코딩돼 있어서 프리셋을 바꿔도 마우스
오버 시 원래 그린이 튀어나오는 버그가 있었다(사후 수정함). 브랜드 색 관련 하드코딩을 찾을 땐
`grep -n ":hover"` 결과에서 `#[0-9a-fA-F]{6}` 패턴을 같이 훑어 "base는 var(--portal-primary)인데
hover만 리터럴 hex인" 규칙이 남아있지 않은지 확인할 것.

### board 스킨 (`skin/board/*/style.css`, `mobile/skin/board/*/style.css`)

board 스킨들은 각자 자기 네임스페이스의 로컬 CSS 변수를 이미 쓰고 있었다(예:
`cafe_style/style.css`의 `--cs-primary`/`--cs-primary-dk`, `mobile/skin/board/gallery`의
`--mg-primary`/`--mg-primary-dk`, `mobile/skin/board/mobile`의 `--mb-primary`/`--mb-primary-dk`
와 `--bsk-primary`/`--bsk-primary-dk` 두 세트). 이 로컬 변수의 **정의부만** 하드코딩된 hex 대신
`var(--portal-primary)`/`var(--portal-primary-hover)`를 가리키도록 바꿔주면, 그 변수를 쓰는
파일 내 나머지 규칙(수십 곳)이 전부 자동으로 테마 색을 따라간다 — 각 규칙을 일일이 치환할 필요
없음. `:root`는 문서 전역이라 `default.css`가 로드되는 페이지라면 어느 스킨 CSS 파일에서도
`var(--portal-primary)`를 바로 참조할 수 있다(모바일 테마도 별도 head.php 없이 동일한
`head.sub.php`를 공유하므로 마찬가지).

**주의**: `mobile/skin/board/mobile/style.css`의 `.bsk_share_naver { background: #03c75a; ... }`
(507번째 줄)는 **건드리면 안 된다** — 이건 테마 포인트 컬러가 아니라 "네이버로 공유하기" 버튼의
실제 네이버 브랜드 로고 색이며, 우연히 이 테마의 기존 기본색과 hex가 같아서 헷갈리기 쉽다
(카카오 `#fee500`, 페이스북 `#1877f2` 공유 버튼처럼 항상 고정색이어야 하는 것과 같은 종류).
새 스킨을 손볼 때도 `#03c75a`가 보이면 그게 테마 포인트 컬러인지 아니면 이런 고정 브랜드
색상인지부터 문맥으로 구분할 것. 아직 안 건드린 다른 스킨(`skin/latest`, `skin/faq`,
`skin/member`, `skin/search`, `skin/board/webzine`, `skin/board/_base`, `skin/board/list` 등)에도
비슷한 하드코딩이 남아있을 수 있으니, 관련 이슈가 보고되면 같은 패턴(로컬 `:root` 변수 정의부만
치환)으로 처리하면 된다.

- 프리셋 정의: `portal.settings.php`의 `PORTAL_THEME_COLOR_PRESETS`(`primary`/`hover`
  hex와 라벨). `default` 키는 항상 원래 그린(`#03c75a`/`#02b050`)과 정확히 같아야 한다 —
  hover 값을 `portal_theme_darken_hex()`로 자동 계산하지 않고 명시적으로 고정해 둔 이유가
  이것이다(자동 계산식은 원래 hover 색과 정확히 일치하지 않는다). 새 프리셋을 추가할 때는
  이 배열에 한 줄만 추가하면 관리자 UI 스와치/저장 검증/실제 반영까지 전부 자동으로 반영된다.
- 설정 저장: `theme_color` 키(`portal.settings.php`의 `$defaults`, 기본값 `'default'`).
  `portal.save.php`는 `PORTAL_THEME_COLOR_PRESETS`에 없는 키가 오면 무조건 `'default'`로
  강제한다(화이트리스트 검증).
- 실제 반영: `head.sub.php`가 `portal.settings.php`를 직접 `require_once`한 뒤
  `portal_theme_colors()`(현재 설정에 맞는 primary/hover hex 반환)의 값으로
  `<style>:root{--portal-primary:..;--portal-primary-hover:..;}</style>`를 `default.css`
  `<link>` 바로 뒤에 인라인 출력한다 — 기본값이어도 항상 출력(분기 단순화 목적, 출력 결과는
  어차피 CSS 파일 기본값과 동일).
- 관리자 UI: `index.php`의 "색상" 탭이 `PORTAL_THEME_COLOR_PRESETS`를 순회해 색상 스와치
  버튼을 렌더링(`data-color` 속성에 프리셋 키). 클릭 시 `pe_theme_color` hidden input에
  키를 저장하고 `.selected` 클래스를 토글한다.

## board 목록 카드에서 sv_use(사이드뷰)를 <a> 안에 넣으면 안 됨

`get_sideview()`(`lib/common.lib.php`)가 만드는 `sv_use` 마크업은 그 자체로 `<a class="sv_member">`
를 포함하고, 클릭 시 열리는 프로필 팝업(`.sv_wrap .sv`)에도 `<a>`가 여러 개 더 들어있다. 이걸
게시글로 이동하는 카드 링크 `<a>` **안에** 그대로 넣으면 `<a>` 안에 `<a>`가 중첩되는 잘못된
HTML이 되어 브라우저가 바깥 `<a>`를 사이드뷰가 시작하는 지점에서 강제로 끊어버린다. 그
결과로 (1) 제목·요약·메타 정보가 의도한 flex/grid 박스 밖으로 새어나가 스타일이 안 먹은 채
한 줄로 쭉 붙어 보이고, (2) 사이드뷰 팝업도 카드 스타일(`overflow:hidden`)에 잘리거나 클릭이
씹히는 등 "그대로" 출력되는 문제로 나타난다.

`gallery` 스킨(`skin/board/gallery/list.skin.php`)이 처음부터 이 문제를 피해 간 정석 패턴이다:
- 카드 전체를 감싸는 `<a>`를 쓰지 않는다. **썸네일만** 별도 `<a>`로 감싸고, **제목도** 별도의
  `<a>`로 감싼다(`bsk_gall_link`, `bsk_gall_tit_link`처럼 링크를 쪼갠다).
- `sv_use`가 들어가는 메타 영역(`bsk_gall_meta`)은 어떤 `<a>`에도 속하지 않는 평범한 컨테이너에 둔다.
- 카드의 테두리/호버 효과(그림자, `translateY`, 썸네일 확대)는 안쪽 `<a>`가 아니라 **바깥
  `<li>`(카드 자체)** 에 걸어서, 링크를 쪼개도 "카드 전체에 마우스를 올리면 반응" 하는 느낌은
  그대로 유지한다.
- 카드 자체에는 `overflow:hidden`을 걸지 않는다(사이드뷰 팝업이 `position:absolute`로 카드
  밖까지 나가야 하므로). 모서리를 둥글게 깎아야 하는 시각 요소(썸네일)에만 개별적으로
  `border-radius`+`overflow:hidden`을 준다.

`webzine` 스킨(`skin/board/webzine/list.skin.php`)은 원래 이 패턴을 따르지 않고 featured
카드 전체와 그리드 카드 전체를 하나의 `<a>`로 감싸고 있었다(`bsk_wz_featured_link`,
`bsk_wz_item_link`). 그 안에 `sv_use`가 들어간 메타 영역까지 포함돼 있어서 정확히 위 증상이
났고, 위 gallery 패턴 그대로 링크를 썸네일 전용(`bsk_wz_featured_thumb_link`,
`bsk_wz_item_thumb_link`)과 제목 전용(`bsk_wz_featured_tit_link`, `bsk_wz_item_tit_link`)으로
쪼개고, 호버 효과를 `.bsk_wz_featured`/`.bsk_wz_item`(카드 자체)로 옮기고, 카드 레벨
`overflow:hidden`을 제거해 고쳤다. **다른 스킨에 새 목록 카드를 만들 때는 처음부터 이 패턴을
따를 것** — `sv_use`(또는 `get_sideview()`를 거치는 어떤 값이든)를 링크 텍스트 일부로 감싸는
대신 반드시 카드 링크 바깥의 별도 블록에 둔다. 아직 감사하지 않은 `list`/`cafe_style` 스킨에도
같은 패턴의 버그가 남아있을 수 있다.

## 다크모드 (헤더 우측 토글)

**범위는 "핵심 레이아웃"만이다 — 전체 사이트가 아니다.** 적용 대상: Top Header/Header/GNB,
`_sidebar.php` 위젯, 홈 화면 카드(`.gallery_section`/`.half_board`/`.nv_news_item`/`.portal_tabs`),
푸터, 게시판 목록·보기의 **기본** 배경/텍스트(`.bsk_list_header`/`.bsk_list_item`/`.bsk_view_tit`/
`.bsk_view_content`/`.bsk_view_name`/`.bsk_total`), 내용관리 페이지(`skin/content/portal_basic`,
PC·모바일 둘 다 — `#ctt`/`#ctt_header`/`#ctt_title`/`#ctt_admin`/`#ctt_con`). **적용 안 된 곳**:
글쓰기/답글 작성 폼, 게시판의 버튼·배지·페이저·검색모달·추천·댓글옵션·자동저장 팝업 같은
`.bsk_*` 세부 컴포넌트(자체 배경이 있어 다크 배경 위에서도 깨지진 않지만 라이트 스타일 그대로임),
관리자 편집 모달(`pe_*`), `plugin/portal-search-page` 검색결과 페이지, `about.php`/`guide.php`
(내용관리 co_id가 `company`/`guide`일 때 `content.skin.php`가 위임하는 별도 파일 — `skin/content`와
같은 하드코딩 패턴이 남아있지만 아직 안 고침). 새로 어두운 색이 필요한 영역이 생기면 아래 변수만
참조하도록 확장하면 되고, 손대지 않은 영역은 위 목록에서 지우면 된다.

**주의(내용관리 co_skin)**: 게시글/위젯과 달리 내용관리(`bbs/content.php`)는 각 `co_id`마다
DB의 `g5_content.co_skin`/`co_mobile_skin` 값으로 스킨을 **개별 선택**한다. 이 값이
`theme/portal_basic`이면 위에서 다크모드 대응한 `skin/content/portal_basic/content.skin.php`를
타지만, 그냥 `basic`(접두어 `theme/` 없음)이면 `get_skin_path()`가 테마를 거치지 않고 **코어
`skin/content/basic/content.skin.php` + `style.css`**로 가버린다 — 이 코어 스킨은 `#ctt {
background:#fff }`만 있고 글자색은 아예 지정을 안 해서 body의 다크모드 텍스트 색(`var(--portal-text)`)을
그대로 상속받는다. 그 결과 배경은 라이트 흰색 그대로인데 글자만 다크모드용 밝은 회색으로 바뀌어
거의 안 보이는 정반대 방향의 버그가 난다. 실제로 이 사이트는 `privacy`/`provision`/`noemail`/
`disclaimer` 4개 co_id가 `co_skin='basic'`으로 남아있어서 겪었던 문제이고, `UPDATE g5_content
SET co_skin='theme/portal_basic', co_mobile_skin='theme/portal_basic' WHERE co_id IN (...)`로
테마 스킨으로 전환해서 해결했다(코어 파일은 건드리지 않음 — 다른 사이트와 공유되는 코어 스킨을
고치는 것보다 이미 다크모드 대응된 테마 스킨으로 리다이렉트하는 편이 안전). **내용관리 관련
다크모드 리포트가 들어오면 먼저 `SELECT co_id, co_skin, co_mobile_skin FROM g5_content;`로
`basic`(테마 접두어 없는 값)이 남아있는지부터 확인할 것** — 관리자가 새 내용관리 항목을
추가할 때도 스킨을 `theme/portal_basic`으로 선택해야 다크모드가 먹는다.

**주의**: `_sidebar.php` 위젯은 "적용 대상"에 들어있었지만, 위젯 안 게시글 제목 텍스트
색상(`.widget_title a`, `.pl_link`/`.pl_tit`/`.pl_notice .pl_tit`, `.sl_link`, `.pa_title`,
`.pl_gal_tit`)이 `#333`/`#1a1a1a`/`#555`로 하드코딩돼 있어서 실제로는 다크모드에서 위젯 배경만
어두워지고 글 제목은 라이트 모드 색 그대로 남아 거의 안 보이는 버그가 있었다(사후 수정함,
전부 `var(--portal-text)`로 교체). "적용 대상" 목록에 있다고 해서 그 안의 모든 하드코딩이
실제로 치환됐다는 보장은 없으니, 비슷한 리포트가 들어오면 목록에 있는 영역이라도
`grep -n "#[0-9a-fA-F]\{3,6\}"`로 그 섹션을 다시 훑어볼 것.

- **색상 변수**: `css/default.css` 최상단 `:root`에 `--portal-bg`(페이지 배경),
  `--portal-surface`(카드/헤더/GNB 배경, 원래 `#fff`이던 곳), `--portal-surface-alt`(옅은
  회색 배경, 원래 `#f8f8f8`/`#f8f9fa`이던 곳), `--portal-border`(구분선, 원래 `#ebebeb`류),
  `--portal-text`(본문 텍스트, 원래 `#1a1a1a`류), `--portal-text-sub`(보조/흐린 텍스트, 원래
  `#888`류) 를 선언하고, `html[data-theme="dark"] { ... }` 셀렉터에서 전부 재정의한다. 브랜드
  색(`--portal-primary`)과 마찬가지로 **hex를 직접 쓰지 말고 반드시 이 변수를 쓸 것** —
  안 그러면 다크모드에서 그 부분만 라이트로 남는다. 여러 회색조(`#333`/`#444`/`#555`/`#666`/
  `#888`/`#aaa`/`#bbb`)를 전부 구분하지 않고 `--portal-text`/`--portal-text-sub` 둘로만
  단순화했으므로, 새 텍스트 색이 필요하면 "진하면 text, 흐리면 text-sub" 기준으로 고를 것.
- **토글 스위치**: 켜고 끄는 상태 저장은 `localStorage`(`portal_theme` 키, `'dark'`/`'light'`)
  이고, DOM 반영은 `<html>` 태그의 `data-theme="dark"` 속성 유무로 한다. 저장값이 없으면
  `prefers-color-scheme: dark` 시스템 설정을 기본값으로 따른다.
- **FOUC 방지**: `head.sub.php`가 `<head>` 여는 태그 바로 다음(첫 CSS `<link>`보다도 앞)에
  차단(blocking) 인라인 `<script>`를 심어서, 저장된 선택을 CSS가 그려지기 전에 `<html>`에
  미리 반영한다. `G5_IS_ADMIN`일 때는 이 스크립트도, 토글 버튼도 전부 건너뛴다(관리자 화면은
  포털 테마 CSS 자체가 안 실리므로 무관).
- **토글 버튼**: `head.php`의 `#portal_header .dark_mode_toggle` — `.header_banner` 바로
  뒤, `.mobile_menu_btn` 바로 앞에 있다. `margin-left: auto`를 직접 갖고 있어서 헤더 배너가
  있든 없든 항상 헤더 우측 끝에 붙는다(배너가 있으면 배너의 auto 마진이 여백을 이미 다
  써버려서 이 버튼의 auto 마진은 0으로 계산됨 — `#portal_header .inner`에
  `justify-content: space-between`을 쓰지 않는 기존 관례와 동일한 원리, 위 "레이아웃 주의사항"
  절 참고). 모바일 flex-wrap 레이아웃에서는 `order: 2`(검색플러그인 관리자 버튼과 동일 순서)로
  재배치되고 `margin-left`는 `0`으로 초기화된다. 클릭 핸들러는 `head.php` 하단 스크립트에
  있고, 아이콘은 `fa-moon-o`(라이트일 때, "다크로 전환") ↔ `fa-sun-o`(다크일 때)로 바뀐다.

## ToTop 버튼

`tail.php`의 `#portal_totop_btn` — 푸터 뒤, `#portal_wrap` 안에 있는 `position: fixed`
버튼이라 DOM 위치는 스크롤/노출 동작에 영향 없다. `scrollTop > 300px`일 때만 `.show` 클래스가
붙어 나타나고(`tail.php`의 스크롤 리스너), 클릭하면 jQuery `animate(scrollTop:0)`로 부드럽게
맨 위로 이동한다. 배경색은 `var(--portal-primary)`라 색상 프리셋을 바꾸면 같이 바뀐다. 모든
페이지의 `tail.php`를 거치는 화면에 공통으로 노출된다(게시판 글쓰기/보기 포함, portal-search-page
같은 독립 진입점은 이 파일을 거치지 않으므로 노출되지 않음).

## 관련 파일

| 파일 | 역할 |
|---|---|
| `portal.settings.php` | 설정 기본값, 로드/캐시, 회원 제외 헬퍼, 게시판 목록/최신글/인기글 헬퍼 |
| `portal.save.php` | 설정 저장 AJAX 엔드포인트 (최고관리자 전용) |
| `portal.upload.php` | 이미지 업로드 AJAX 엔드포인트 (최고관리자 전용) |
| `head.php` | Top Header + Header(다크모드 토글 포함) + GNB 렌더링 |
| `head.sub.php` | `<head>` 태그 직접 관리(코어 head.sub.php 대체). 다크모드 FOUC 방지 스크립트, 테마 CSS/포인트 컬러 `<style>` 출력 |
| `tail.php` | 푸터 + ToTop 버튼 렌더링, `</body></html>` 마감 |
| `index.php` | 홈 화면 + 테마 편집 모달(관리자 UI) + 저장/업로드 연동 JS |
| `_board_sidebar.php` | 게시판 사이드바(광고 포함) |
| `css/default.css` | 전체 테마 스타일 (Top Header/Header/헤더배너 관련 규칙은 "Header" 섹션, 다크모드 변수는 "다크모드" 섹션 참고) |

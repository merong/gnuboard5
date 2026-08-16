# 그누보드5 네이버 카페 스타일 게시판 스킨 제작 가이드

## 1. 개요

- **목표**: 그누보드5 기본 게시판 스킨을 수정하여, 네이버 카페 특유의 조밀하고 깔끔한 리스트 형태의 게시판 스킨 제작
- **스킨 경로**: /theme/portal/skin/board/cafe_style/
- **참고 이미지 분석 (image_979322.jpg)**:좌우 테두리가 없고 상하 가로선만 얇게 존재하는 깔끔한 테이블 디자인.
- 폰트 크기가 작고 행간이 좁아 한 화면에 많은 글을 보여줌.
- '공지' 뱃지가 눈에 띄는 색상(연분홍 배경에 붉은 텍스트)으로 처리됨.
- 새글(N), 댓글 수([10]), 이미지 첨부 아이콘 등이 제목 우측에 컴팩트하게 배치됨.
- 하단에는 네이버 특유의 녹색 포인트(#00c73c 또는 #03c75a)를 사용한 글쓰기 버튼, 검색바, 페이징이 중앙 정렬됨.

## 2. 세부 수정 지침 (AI 코딩 요청용)

AI 어시스턴트(Claude 등)에게 코드를 요청할 때, 아래의 지침을 바탕으로 list.skin.php와 style.css를 작성하도록 명령하세요.

### [HTML / PHP 구조 변경 사항 (list.skin.php)]

- **공지사항 표시 (Notice)**기존 '공지'라는 텍스트 대신, <span class="cafe_notice_badge">공지</span> 형태로 마크업을 변경할 것.

- **제목 열 (Subject)**댓글 수는 붉은색(color: #ff0000;)으로 표시. 예: <span class="cnt_cmt">[15]</span>
- 새글 아이콘(N)은 작고 귀여운 느낌의 뱃지로 처리.

- **하단 레이아웃 (Bottom)**페이징(pg_wrap)을 중앙 정렬할 것.
- 검색창(bo_sch)과 글쓰기 버튼을 하단 중앙에 일렬로 예쁘게 배치. 검색 버튼과 글쓰기 버튼은 네이버 그린 컬러를 사용할 것.

### [CSS 스타일링 요구사항 (style.css)]

/* 기본 테이블 스타일 */
.board_list table { width: 100%; border-collapse: collapse; border-spacing: 0; }
.board_list th { padding: 10px 5px; border-top: 2px solid #555; border-bottom: 1px solid #e4e8eb; background: #f9f9f9; color: #333; font-size: 13px; font-weight: normal; }
.board_list td { padding: 8px 5px; border-bottom: 1px solid #f2f2f2; color: #555; font-size: 13px; text-align: center; }

/* 세로 선 제거 (네이버 카페 특징) */
.board_list th, .board_list td { border-left: none; border-right: none; }

/* 제목 열 정렬 및 스타일 */
.board_list td.td_subject { text-align: left; padding-left: 10px; }
.board_list td.td_subject a { color: #333; text-decoration: none; }
.board_list td.td_subject a:hover { text-decoration: underline; }

/* 댓글수 및 아이콘 */
.cnt_cmt { color: #ff3a48; font-size: 12px; margin-left: 3px; font-weight: bold; }
.icon_new { display: inline-block; background: #ff4747; color: #fff; font-size: 10px; padding: 1px 4px; border-radius: 3px; margin-left: 3px; vertical-align: middle; }

/* 공지사항 뱃지 */
.cafe_notice_badge { display: inline-block; background: #ffeeee; color: #ff3a48; font-size: 11px; padding: 2px 6px; border-radius: 10px; border: 1px solid #ffcdcd; }

/* 행 호버 효과 */
.board_list tbody tr:hover td { background-color: #fafbfc; }

/* 하단 검색 및 버튼 영역 */
.bo_fx { text-align: center; margin-top: 20px; }
.btn_b02 { background: #fff; border: 1px solid #ccc; color: #333; padding: 6px 12px; border-radius: 2px; cursor: pointer; }
.btn_submit, .btn_b01 { background: #03c75a; border: 1px solid #03c75a; color: #fff; padding: 6px 15px; border-radius: 2px; font-weight: bold; cursor: pointer; }

## 3. Claude 대상 프롬프트 예시 (Copy & Paste)

현재 그누보드5 포털 테마를 만들고 있습니다. 
기본 게시판 스킨(`basic`)을 바탕으로 네이버 카페 스타일의 게시판 스킨(`cafe_style`)을 제작하려고 합니다. 
위에 작성된 `네이버 카페 스타일 게시판 스킨 가이드`의 요구사항과 CSS 명세서를 참고하여, 그누보드의 `list.skin.php`의 전체 HTML/PHP 코드와 완벽하게 호환되는 `style.css` 코드를 작성해 주세요. 
특히 테이블의 세로선이 없고 상하선만 얇게 있는 조밀한 리스트 형태와, 연분홍색의 '공지' 뱃지, 그리고 녹색 포인트의 하단 검색바에 집중해 주세요.

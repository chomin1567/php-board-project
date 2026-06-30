<?php
session_start();
require 'db.php'; /* DB 연결 */

/* 로그인 안 했으면 로그인 페이지로 이동 */
if (!isset($_SESSION['user_id'])) {
    header('Location: ../board/login.php');
    exit;
}

/* CSRF 토큰 검증 */
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("잘못된 요청입니다.");
}

/* 폼에서 게시글 id와 댓글 내용 가져오기 */
$post_id = $_POST['post_id'];
$content = $_POST['content'];
$author_id = $_SESSION['user_id'];

/* 댓글 DB에 저장 */
$stmt = $pdo->prepare("INSERT INTO notice_comments (post_id, content, author_id) VALUES (?, ?, ?)");
$stmt->execute([$post_id, $content, $author_id]);

/* 댓글 등록 후 게시글 페이지로 이동 */
header('Location: view.php?id=' . $post_id);
exit;
?>
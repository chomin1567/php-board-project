<?php
session_start();
require 'db.php'; /*DB 연결*/

/* 로그인 안 했으면 로그인 페이지로 이동 */
if (!isset($_SESSION['user_id'])) {
    header('Location: ../board/login.php');
    exit;
}
/* CSRF 토큰 검증 */
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("잘못된 요청입니다.");
}


/* POST 요청에서 댓글 id와 게시글 id를 가져옴 */
$id = $_POST['id'];
$post_id = $_POST['post_id'];
 /* 가져온 해당 댓글 삭제 */
$stmt = $pdo->prepare("DELETE FROM notice_comments WHERE id=?");
$stmt->execute([$id]);

/* 삭제 후 게시글 페이지로 이동함 */
header('Location: view.php?id=' . $post_id);
exit;
?>

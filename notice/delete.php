<?php
session_start();
require 'db.php'; /*DB 연결*/

/* 로그인 안 했으면 로그인 페이지로 이동 */
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

/* CSRF 토큰 검증 */
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("잘못된 요청입니다.");
}


$id = $_POST['id']; /* POST 요청에서 게시글 id 가져옴 */


$stmt = $pdo->prepare("DELETE FROM notice_posts WHERE id=?"); /* 가져온 해당 게시글 삭제함 */
$stmt->execute([$id]);

header('Location: index.php');  /* 삭제 후 게시글 목록으로 이동함 */
exit;
?>

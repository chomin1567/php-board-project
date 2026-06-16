<?php
session_start();
require 'db.php'; /*DB 연결*/

/* 로그인 안 했으면 로그인 페이지로 이동 */
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

/* URL에서 댓글 id와 게시글 id를 가져옴 */
$id = $_GET['id'];
$post_id = $_GET['post_id'];
 /* 가져온 해당 댓글 삭제 */
$stmt = $pdo->prepare("DELETE FROM comments WHERE id=?");
$stmt->execute([$id]);

/* 삭제 후 게시글 페이지로 이동함 */
header('Location: view.php?id=' . $post_id);
exit;
?>

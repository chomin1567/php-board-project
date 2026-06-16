<?php
session_start();
require 'db.php'; /*DB 연결*/

/* 로그인 안 했으면 로그인 페이지로 이동 */
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

/* 폼에서 게시글 id와 댓글 내용 가져옴 */
$post_id = $_POST['post_id'];
$content = $_POST['content'];
$author_id = $_SESSION['user_id'];

/* 댓글 DB에 저장함 */
$stmt = $pdo->prepare("INSERT INTO comments (post_id, content, author_id) VALUES (?, ?, ?)");
$stmt->execute([$post_id, $content, $author_id]);

/* 댓글 등록 후 게시글 페이지로 이동 */
header('Location: view.php?id=' . $post_id);
exit;
?>

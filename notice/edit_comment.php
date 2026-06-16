<?php
session_start();
require 'db.php'; /* DB 연결 */

/* 로그인 안 했으면 로그인 페이지로 이동 */
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

/* URL에서 댓글 id와 게시글 id 가져오기 */
$id = $_GET['id'];
$post_id = $_GET['post_id'];

/* 수정 폼 제출 시 (POST 요청) */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];

    /* 댓글 내용 수정 */
    $stmt = $pdo->prepare("UPDATE notice_comments SET content=? WHERE id=?");
    $stmt->execute([$content, $id]);

    /* 수정 후 게시글 페이지로 이동 */
    header('Location: view.php?id=' . $post_id);
    exit;
}

/* 기존 댓글 내용 불러오기 */
$stmt = $pdo->prepare("SELECT * FROM notice_comments WHERE id=?");
$stmt->execute([$id]);
$comment = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>댓글 수정</title>
</head>
<body>
    <h1>댓글 수정</h1>
    <form method="POST">
        <textarea name="content"><?= htmlspecialchars($comment['content']) ?></textarea>
        <button type="submit">수정완료</button>
    </form>
    <a href="view.php?id=<?= $post_id ?>">취소</a>
</body>
</html>
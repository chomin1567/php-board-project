<?php
session_start();
require 'db.php'; /*DB 연결*/

/* 로그인 안 했으면 로그인 페이지로 이동 */
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id']; /* URL에서 게시글 id 가져옴 */

/* 수정 폼 제출할 때 (POST 요청함) */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];     /* 수정된 제목 가져옴 */
    $content = $_POST['content']; /* 수정된 내용 가져옴 */

    /* 게시글 제목, 내용 수정함 */
    $stmt = $pdo->prepare("UPDATE posts SET title=?, content=? WHERE id=?");
    $stmt->execute([$title, $content, $id]);

    /* 수정 후 게시글 페이지로 이동함 */
    header('Location: view.php?id=' . $id);
    exit;
}

/* 기존 게시글 내용 불러옴(그래야 수정하기 편함) */
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id=?");
$stmt->execute([$id]);
$post = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>게시글 수정</title>
</head>
<body>
  <h1>게시글 수정</h1>
  <form method="POST">
    <p>제목: <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>"></p>
    <p>내용: <textarea name="content"><?= htmlspecialchars($post['content']) ?></textarea></p>
    <button type="submit">수정완료</button>
  </form>
  <a href="view.php?id=<?= $id ?>">취소</a>
</body>
</html>

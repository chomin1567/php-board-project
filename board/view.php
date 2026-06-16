<?php
session_start();
require 'db.php'; /*DB 연결*/

/* 로그인 안 했으면 로그인 페이지로 이동 */
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

/* URL에서 게시글 id 가져옴 */
$id = $_GET['id'];

/* 게시글 내용을 작성자 이름과 같이 가져옴 */
$stmt = $pdo->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.author_id = users.id WHERE posts.id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

/* 게시글의 댓글 목록을 작성자 이름과 같이 오래된 순으로 가져오는 형식임 */
$stmt2 = $pdo->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.author_id = users.id WHERE comments.post_id = ? ORDER BY comments.created_at ASC");
$stmt2->execute([$id]);
$comments = $stmt2->fetchAll();

/* 첨부파일 목록 가져오기 */
$stmt3 = $pdo->prepare("SELECT * FROM attachments WHERE post_id = ?");
$stmt3->execute([$id]);
$attachments = $stmt3->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($post['title']) ?></title>
</head>
<body>
  <h1><?= htmlspecialchars($post['title']) ?></h1>
  <p>작성자: <?= htmlspecialchars($post['username']) ?></p>
  <p>날짜: <?= $post['created_at'] ?></p>
  <hr>
  <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
  <a href="edit.php?id=<?= $id ?>">수정</a>
  <a href="delete.php?id=<?= $id ?>">삭제</a>
  <a href="index.php">목록으로</a>

  <?php if ($attachments): ?>
  <h2>첨부파일</h2>
  <?php foreach ($attachments as $file): ?>
  <p><a href="download.php?id=<?= $file['id'] ?>"><?= htmlspecialchars($file['original_name']) ?></a></p>
  <?php endforeach; ?>
  <?php endif; ?>

  <h2>댓글</h2>
  <?php foreach ($comments as $comment): ?>
  <p><?= htmlspecialchars($comment['username']) ?>: <?= htmlspecialchars($comment['content']) ?>
  <?php if ($_SESSION['user_id'] == $comment['author_id']): ?>
  <a href="edit_comment.php?id=<?= $comment['id'] ?>&post_id=<?= $id ?>">수정</a>
  <a href="delete_comment.php?id=<?= $comment['id'] ?>&post_id=<?= $id ?>" onclick="return confirm('정말 삭제하시겠습니까?')">삭제</a>
  <?php endif; ?>
  </p>
  <?php endforeach; ?>

  <form method="POST" action="comment.php">
    <input type="hidden" name="post_id" value="<?= $id ?>">
    <textarea name="content"></textarea>
    <button type="submit">댓글 등록</button>
  </form>
</body>
</html>

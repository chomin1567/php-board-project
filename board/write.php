<?php
session_start();
require 'db.php'; /*DB 연결*/

/* 로그인 안 했으면 로그인 페이지로 이동 */
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}


/* 글쓰기 폼 제출할 때 (POST를 요청함) */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author_id = $_SESSION['user_id'];

    /* 게시글 DB에 저장함 */
    $stmt = $pdo->prepare("INSERT INTO posts (title, content, author_id) VALUES (?, ?, ?)");
    $stmt->execute([$title, $content, $author_id]);

    /* 파일 업로드 처리 */
    if (!empty($_FILES['file']['name'])) {
        $post_id = $pdo->lastInsertId();
        $original_name = $_FILES['file']['name'];
        $stored_path = 'uploads/' . time() . '_' . $original_name;
        $size_bytes = $_FILES['file']['size'];

        move_uploaded_file($_FILES['file']['tmp_name'], '/var/www/html/board/' . $stored_path);

        $stmt2 = $pdo->prepare("INSERT INTO attachments (post_id, original_name, stored_path, size_bytes) VALUES (?, ?, ?, ?)");
        $stmt2->execute([$post_id, $original_name, $stored_path, $size_bytes]);
    }

    
    /* 등록 후 게시글 목록으로 이동함 */
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>글쓰기</title>
</head>
<body>
  <h1>글쓰기</h1>
  <form method="POST" enctype="multipart/form-data">
    <p>제목: <input type="text" name="title"></p>
    <p>내용: <textarea name="content"></textarea></p>
    <p>파일: <input type="file" name="file"></p>
    <button type="submit">등록</button>
  </form>
  <a href="index.php">목록으로</a>
</body>
</html>

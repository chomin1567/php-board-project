<?php
session_start();
require 'db.php'; /*DB 연결*/

/* 로그인 안 했으면 로그인 페이지로 이동 */
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
/* CSRF 토큰 생성 */
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* 글쓰기 폼 제출할 때 (POST를 요청함) */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /* CSRF 토큰 검증 */
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("잘못된 요청입니다.");
    }

    $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars($_POST['content'], ENT_QUOTES, 'UTF-8');
    $author_id = $_SESSION['user_id'];

    /* 게시글 DB에 저장함 */
    $stmt = $pdo->prepare("INSERT INTO notice_posts (title, content, author_id) VALUES (?, ?, ?)");
    $stmt->execute([$title, $content, $author_id]);

    /* 파일 업로드 처리 */
    if (!empty($_FILES['file']['name'])) {
        /* 허용할 확장자 목록 */
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt', 'zip'];
        /* 최대 파일 크기 (5MB) */
        $max_size = 5 * 1024 * 1024;

        $original_name = $_FILES['file']['name'];
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        $size_bytes = $_FILES['file']['size'];

        if (!in_array($ext, $allowed_ext)) {
            die("허용되지 않는 파일 형식입니다.");
        }
        if ($size_bytes > $max_size) {
            die("파일 크기가 5MB를 초과합니다.");
        }

        $post_id = $pdo->lastInsertId();
        $stored_path = 'uploads/' . time() . '_' . $original_name;
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
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <p>제목: <input type="text" name="title"></p>
        <p>내용: <textarea name="content"></textarea></p>
        <p>파일: <input type="file" name="file"></p>
        <button type="submit">등록</button>
    </form>
    <a href="index.php">목록으로</a>
</body>

</html>
<?php
session_start();
require 'db.php'; /*DB 연결*/

/* 로그인 안 했으면 로그인 페이지로 이동 */
if (!isset($_SESSION['user_id'])) {
    header('Location: ../board/login.php');
    exit;
}

/* CSRF 토큰 생성 */
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$id = $_GET['id']; /* URL에서 게시글 id 가져옴 */

/* 수정 폼 제출할 때 (POST 요청함) */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /* CSRF 토큰 검증 */
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("잘못된 요청입니다.");
    }

    $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars($_POST['content'], ENT_QUOTES, 'UTF-8');

    /* 게시글 제목, 내용 수정함 */
    $stmt = $pdo->prepare("UPDATE notice_posts SET title=?, content=? WHERE id=?");
    $stmt->execute([$title, $content, $id]);

    /* 새 파일 업로드 시 기존 파일 삭제 후 새 파일 저장 */
    if (!empty($_FILES['file']['name'])) {
        $stmt2 = $pdo->prepare("SELECT * FROM notice_attachments WHERE post_id = ?");
        $stmt2->execute([$id]);
        $old_file = $stmt2->fetch();
        if ($old_file) {
            unlink('/var/www/html/notice/' . $old_file['stored_path']);
            $stmt2 = $pdo->prepare("DELETE FROM notice_attachments WHERE post_id = ?");
            $stmt2->execute([$id]);
        }

        $original_name = $_FILES['file']['name'];
        $stored_path = 'uploads/' . time() . '_' . $original_name;
        $size_bytes = $_FILES['file']['size'];
        move_uploaded_file($_FILES['file']['tmp_name'], '/var/www/html/notice/' . $stored_path);

        $stmt2 = $pdo->prepare("INSERT INTO notice_attachments (post_id, original_name, stored_path, size_bytes) VALUES (?, ?, ?, ?)");
        $stmt2->execute([$id, $original_name, $stored_path, $size_bytes]);
    }

    header('Location: view.php?id=' . $id);
    exit;
}

/* 기존 게시글 내용 불러옴 */
$stmt = $pdo->prepare("SELECT * FROM notice_posts WHERE id=?");
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
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <p>제목: <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>"></p>
        <p>내용: <textarea name="content"><?= htmlspecialchars($post['content']) ?></textarea></p>
        <p>파일: <input type="file" name="file"></p>
        <button type="submit">수정완료</button>
    </form>
    <a href="view.php?id=<?= $id ?>">취소</a>
</body>

</html>
<?php
session_start();

/* 로그인 안 했으면 board의 로그인 페이지로 이동 */
if (!isset($_SESSION['user_id'])) {
    header('Location: board/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>K.Knock 게시판</title>
</head>
<body>
    <h1>K.Knock 게시판</h1>
    <p>안녕하세요, <?= htmlspecialchars($_SESSION['username']) ?>님! <a href="board/logout.php">로그아웃</a></p>
    <p>원하는 게시판을 선택해주세요.</p>
    <ul>
        <li><a href="board/index.php">자유게시판</a></li>
        <li><a href="notice/index.php">공지사항</a></li>
    </ul>
</body>
</html>
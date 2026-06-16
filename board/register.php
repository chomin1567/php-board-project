<?php
require 'db.php';

/* 회원가입 폼 제출 시 (POST 요청) */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); /* 비밀번호 암호화 */

    /* 아이디 중복 확인 */
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        $error = "이미 사용중인 아이디입니다.";
    } else {
        /* 회원 DB에 저장 */
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $password]);
        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>회원가입</title>
</head>
<body>
  <h1>회원가입</h1>
  <?php if (isset($error)): ?>
  <p style="color:red"><?= $error ?></p>
  <?php endif; ?>
  <form method="POST">
    <p>아이디: <input type="text" name="username"></p>
    <p>비밀번호: <input type="password" name="password"></p>
    <button type="submit">가입하기</button>
  </form>
  <a href="login.php">로그인</a>
</body>
</html>
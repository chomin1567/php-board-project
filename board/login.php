<?php
session_start();
require 'db.php';

/* 로그인 폼 제출 시 (POST 요청) */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  /* 아이디로 사용자 찾기 */
  $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->execute([$username]);
  $user = $stmt->fetch();

  /* 비밀번호 확인 */
  if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true); /* 세션 고정 공격 방어 */
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    header('Location: ../index.php');
    exit;
  } else {
    $error = "아이디 또는 비밀번호가 틀렸습니다.";
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>로그인</title>
</head>

<body>
  <h1>로그인</h1>
  <?php if (isset($error)): ?>
    <p style="color:red"><?= $error ?></p>
  <?php endif; ?>
  <form method="POST">
    <p>아이디: <input type="text" name="username"></p>
    <p>비밀번호: <input type="password" name="password"></p>
    <button type="submit">로그인</button>
  </form>
  <a href="register.php">회원가입</a>
</body>

</html>
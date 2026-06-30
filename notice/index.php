<?php
session_start();
require 'db.php'; /*DB 연결*/

/* 로그인 안 했으면 로그인 페이지로 이동 */
if (!isset($_SESSION['user_id'])) {
    header('Location: ../board/login.php');
    exit;
}

/* 검색어 설정 */
$search = $_GET['search'] ?? '';

/* 정렬 방식 설정 DESC(최신순) , ASC(오래된순) */
$sort = $_GET['sort'] ?? 'desc';
$order = $sort === 'asc' ? 'ASC' : 'DESC';

/* 검색 타입 설정 */
$search_type = $_GET['search_type'] ?? 'post';

/* 검색어 있으면 검색, 없으면 전체 목록 */
if ($search) {
    if ($search_type === 'user') {
        $stmt = $pdo->prepare("SELECT notice_posts.*, users.username FROM notice_posts JOIN users ON notice_posts.author_id = users.id WHERE users.username LIKE ? ORDER BY notice_posts.created_at {$order}");
        $stmt->execute(['%' . $search . '%']);
    } else {
        $stmt = $pdo->prepare("SELECT notice_posts.*, users.username FROM notice_posts JOIN users ON notice_posts.author_id = users.id WHERE notice_posts.title LIKE ? OR notice_posts.content LIKE ? ORDER BY notice_posts.created_at {$order}");
        $stmt->execute(['%' . $search . '%', '%' . $search . '%']);
    }
} else {
    $stmt = $pdo->prepare("SELECT notice_posts.*, users.username FROM notice_posts JOIN users ON notice_posts.author_id = users.id ORDER BY notice_posts.created_at {$order}");
    $stmt->execute([]);
}
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>공지사항</title>
</head>
<body>
    <h1>공지사항</h1>
    <p>안녕하세요, <?= htmlspecialchars($_SESSION['username']) ?>님! <a href="logout.php">로그아웃</a></p>
    <a href="write.php">글쓰기</a>
    <form method="GET">
        <input type="text" name="search" placeholder="검색어 입력" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <select name="search_type">
            <option value="post" <?= $search_type === 'post' ? 'selected' : '' ?>>게시글</option>
            <option value="user" <?= $search_type === 'user' ? 'selected' : '' ?>>작성자</option>
        </select>
        <button type="submit">검색</button>
    </form>
    <a href="?sort=desc<?= $search ? '&search='.$search : '' ?>">최신순</a>
    <a href="?sort=asc<?= $search ? '&search='.$search : '' ?>">오래된순</a>
    <table border="1">
        <tr>
            <th>번호</th>
            <th>제목</th>
            <th>작성자</th>
            <th>날짜</th>
        </tr>
        <?php foreach ($posts as $post): ?>
        <tr>
            <td><?= $post['id'] ?></td>
            <td><a href="view.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></td>
            <td><?= htmlspecialchars($post['username']) ?></td>
            <td><?= $post['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
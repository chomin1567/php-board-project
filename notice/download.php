<?php
session_start();
require 'db.php'; /* DB 연결 */

/* URL에서 파일 id 가져오기 */
$id = $_GET['id'];

/* 파일 정보 가져오기 */
$stmt = $pdo->prepare("SELECT * FROM notice_attachments WHERE id = ?");
$stmt->execute([$id]);
$file = $stmt->fetch();

/* 파일 다운로드 처리 */
$filepath = '/var/www/html/board/' . $file['stored_path'];

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file['original_name'] . '"');
header('Content-Length: ' . $file['size_bytes']);

readfile($filepath);
exit;
?>
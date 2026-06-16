<?php
/* 데이터베이스 연결 설정 파일 , 다른 php 파일에서 require 'db.php'로 불러다가 사용함 */
$host = 'localhost';     /* DB 서버 주소 */
$dbname = 'board';       /* 사용할 데이터베이스 이름 */
$username = 'boarduser'; /* DB 접속 계정 */
$password = '1234';      /* DB 접속 비밀번호 */

/* mysql 연결 */
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8;unix_socket=/var/run/mysqld/mysqld.sock", $username, $password);
/* 오류 발생 시 예외를 던지도록 설정한 것 */
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>


CREATE DATABASE board;
USE board;
/* 사용자 테이블: 아이디(중복 불가), 비번, 가입 시간*/
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,    /* 사용자 고유 번호 */
  username VARCHAR(50) NOT NULL UNIQUE, /* 사용자 아이디(중복 불가)*/
  password VARCHAR(255) NOT NULL,       
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

/* 게시글 테이블: id(자동 증가), 제목, 내용, 작성자, 작성/수정 시간*/
CREATE TABLE posts (
  id INT PRIMARY KEY AUTO_INCREMENT, /* 게시글 고유 번호 */
  title VARCHAR(200) NOT NULL,       /* 최대 200자 */
  content TEXT,
  author_id INT NOT NULL,            /* 작성자 id (users 테이블 참조) */
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES users(id) /* 외래키: auther_id는 users테이블의 id를 참조(작성자가 반드시 존재해야 함) */
);
 /* 댓글 테이블: 어느 글(post_id)에, 누가(author_id), 내용을 남겼는지 */
CREATE TABLE comments (
  id INT PRIMARY KEY AUTO_INCREMENT,           /* 댓글 고유 번호 */
  post_id INT NOT NULL,
  author_id INT NOT NULL,
  content TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES posts(id),  /* 외래키: post_id는 posts테이블의 id를 참조 */
  FOREIGN KEY (author_id) REFERENCES users(id) /* 외래키: author_id는 users 테이블의 id를 참조 */
);

/* 첨부파일 테이블: 어느 글에 붙은 파일인지 + 원본 이름, 저장 경로, 크기 */
CREATE TABLE attachments (
  id INT PRIMARY KEY AUTO_INCREMENT,
  post_id INT NOT NULL,
  original_name VARCHAR(255) NOT NULL,  /* 원본 파일 이름 */
  stored_path VARCHAR(500) NOT NULL,    /* 서버에 저장된 경로 */
  size_bytes BIGINT,                    /* 파일 크기 */
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES posts(id)
);

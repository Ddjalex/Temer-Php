-- SQL to create the images table in MariaDB/MySQL
CREATE TABLE IF NOT EXISTS images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    upload_date DATETIME DEFAULT CURRENT_TIMESTAMP
);

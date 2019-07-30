CREATE USER 'bpg_web'@'localhost' IDENTIFIED BY 'aabbccddeeffgghhiijjkkllmmnnoopp';
GRANT USAGE ON *.* TO 'bpg_web'@'localhost';
GRANT SELECT ON `bpg`.`game` TO 'bpg_web'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON `bpg`.`bot` TO 'bpg_web'@'localhost';

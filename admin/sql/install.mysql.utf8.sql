DROP TABLE IF EXISTS `#__attendance_reports`;

CREATE TABLE `#__attendance_reports` ( 
    `id` SERIAL NOT NULL, 
    `present` JSON,
    `absent` JSON,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB; 

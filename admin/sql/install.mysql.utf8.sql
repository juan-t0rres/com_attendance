DROP TABLE IF EXISTS `#__attendance_reports`;

CREATE TABLE `#__attendance_reports` ( 
    `id` SERIAL NOT NULL, 
    `present` JSON,
    `absent` JSON,
    `date_created` VARCHAR(10) NOT NULL,
    `created_by` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB; 

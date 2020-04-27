
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- advanced_description_config
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `advanced_description_config`;

CREATE TABLE `advanced_description_config`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `position` INTEGER DEFAULT 0 NOT NULL,
    `title` VARCHAR(255),
    `structure_bo` LONGTEXT,
    `structure_fo` LONGTEXT,
    `image` VARCHAR(100),
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- advanced_description_object
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `advanced_description_object`;

CREATE TABLE `advanced_description_object`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `hook_code` VARCHAR(50),
    `objtype` INTEGER DEFAULT 0 NOT NULL,
    `objid` INTEGER DEFAULT 0 NOT NULL,
    `structures` VARCHAR(255),
    `variables` LONGTEXT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- advanced_description_object_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `advanced_description_object_i18n`;

CREATE TABLE `advanced_description_object_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `valeurs` LONGTEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `advanced_description_object_i18n_fk_3c7433`
        FOREIGN KEY (`id`)
        REFERENCES `advanced_description_object` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;

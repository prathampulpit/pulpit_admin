ALTER TABLE `drivers` ADD `state` TEXT NULL DEFAULT NULL AFTER `street_address`;


ALTER TABLE `drivers` ADD `issue_date` VARCHAR(255) NULL AFTER `is_busy_at`;

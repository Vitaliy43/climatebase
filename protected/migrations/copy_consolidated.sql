ALTER TABLE `average_max_temperature`  ADD `uniq` VARCHAR(50) NOT NULL;
UPDATE `average_max_temperature` SET uniq = CONCAT(station,'-',period_id) WHERE 1
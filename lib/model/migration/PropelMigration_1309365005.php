<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1309365005.
 * Generated on 2011-06-29 18:30:05 by lezard
 */
class PropelMigration_1309365005
{

	public function preUp($manager)
	{
		// add the pre-migration code here
	}

	public function postUp($manager)
	{
		// add the post-migration code here
	}

	public function preDown($manager)
	{
		// add the pre-migration code here
	}

	public function postDown($manager)
	{
		// add the post-migration code here
	}

	/**
	 * Get the SQL statements for the Up migration
	 *
	 * @return array list of the SQL strings to execute for the Up migration
	 *               the keys being the datasources
	 */
	public function getUpSQL()
	{
		return array (
  'propel' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

CREATE UNIQUE INDEX `hostname` ON `host` (`profile_id`,`room_id`,`number`);

ALTER TABLE `host` ADD CONSTRAINT `host_FK_1`
	FOREIGN KEY (`profile_id`)
	REFERENCES `profile` (`id`);

ALTER TABLE `host` ADD CONSTRAINT `host_FK_2`
	FOREIGN KEY (`room_id`)
	REFERENCES `room` (`id`);

ALTER TABLE `host` ADD CONSTRAINT `host_FK_3`
	FOREIGN KEY (`cloned_from_image_id`)
	REFERENCES `image` (`id`);

ALTER TABLE `host` ADD CONSTRAINT `host_FK_4`
	FOREIGN KEY (`subnet_id`)
	REFERENCES `subnet` (`id`);

ALTER TABLE `host` ADD CONSTRAINT `host_FK_5`
	FOREIGN KEY (`pxe_file_id`)
	REFERENCES `pxe_file` (`id`);

CREATE UNIQUE INDEX `filename` ON `image` (`filename`,`host_id`);

ALTER TABLE `image` ADD CONSTRAINT `image_FK_1`
	FOREIGN KEY (`host_id`)
	REFERENCES `host` (`id`);

ALTER TABLE `image` ADD CONSTRAINT `image_FK_2`
	FOREIGN KEY (`image_server_id`)
	REFERENCES `image_server` (`id`);

CREATE UNIQUE INDEX `image_server_U_1` ON `image_server` (`hostname`);

CREATE UNIQUE INDEX `name` ON `pxe_file` (`name`);

CREATE UNIQUE INDEX `room_U_1` ON `room` (`name`);

CREATE UNIQUE INDEX `ip` ON `subnet` (`ip_address`,`image_server_id`);

ALTER TABLE `subnet` ADD CONSTRAINT `subnet_FK_1`
	FOREIGN KEY (`image_server_id`)
	REFERENCES `image_server` (`id`);

ALTER TABLE `subnet` ADD CONSTRAINT `subnet_FK_2`
	FOREIGN KEY (`pxe_file_id`)
	REFERENCES `pxe_file` (`id`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

	/**
	 * Get the SQL statements for the Down migration
	 *
	 * @return array list of the SQL strings to execute for the Down migration
	 *               the keys being the datasources
	 */
	public function getDownSQL()
	{
		return array (
  'propel' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `host` DROP FOREIGN KEY `host_FK_1`;

ALTER TABLE `host` DROP FOREIGN KEY `host_FK_2`;

ALTER TABLE `host` DROP FOREIGN KEY `host_FK_3`;

ALTER TABLE `host` DROP FOREIGN KEY `host_FK_4`;

ALTER TABLE `host` DROP FOREIGN KEY `host_FK_5`;

DROP INDEX `hostname` ON `host`;

ALTER TABLE `image` DROP FOREIGN KEY `image_FK_1`;

ALTER TABLE `image` DROP FOREIGN KEY `image_FK_2`;

DROP INDEX `filename` ON `image`;

DROP INDEX `image_server_U_1` ON `image_server`;

DROP INDEX `name` ON `pxe_file`;

DROP INDEX `room_U_1` ON `room`;

ALTER TABLE `subnet` DROP FOREIGN KEY `subnet_FK_1`;

ALTER TABLE `subnet` DROP FOREIGN KEY `subnet_FK_2`;

DROP INDEX `ip` ON `subnet`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}
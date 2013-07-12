<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1373624353.
 * Generated on 2013-07-12 12:19:13 by marcelf
 */
class PropelMigration_1373624353
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

ALTER TABLE `image` ADD CONSTRAINT `image_FK_1`
	FOREIGN KEY (`host_id`)
	REFERENCES `host` (`id`);

ALTER TABLE `image` ADD CONSTRAINT `image_FK_2`
	FOREIGN KEY (`image_server_id`)
	REFERENCES `image_server` (`id`);

ALTER TABLE `subnet` CHANGE `dns_server` `dns_server` VARCHAR(40) NOT NULL;

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

ALTER TABLE `image` DROP FOREIGN KEY `image_FK_1`;

ALTER TABLE `image` DROP FOREIGN KEY `image_FK_2`;

ALTER TABLE `subnet` DROP FOREIGN KEY `subnet_FK_1`;

ALTER TABLE `subnet` DROP FOREIGN KEY `subnet_FK_2`;

ALTER TABLE `subnet` CHANGE `dns_server` `dns_server` VARCHAR(15) NOT NULL;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}
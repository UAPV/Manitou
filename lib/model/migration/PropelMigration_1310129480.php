<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1310129480.
 * Generated on 2011-07-08 14:51:20 by lezard
 */
class PropelMigration_1310129480
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
SET FOREIGN_KEY_CHECKS = 0;
ALTER TABLE `host` CHANGE `profile_id` `profile_id` INTEGER NOT NULL;
ALTER TABLE `host` CHANGE `room_id` `room_id` INTEGER NOT NULL;
ALTER TABLE `host` CHANGE `subnet_id` `subnet_id` INTEGER NOT NULL;
ALTER TABLE `subnet` CHANGE `image_server_id` `image_server_id` INTEGER NOT NULL;
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
SET FOREIGN_KEY_CHECKS = 0;
ALTER TABLE `host` CHANGE `profile_id` `profile_id` INTEGER;
ALTER TABLE `host` CHANGE `room_id` `room_id` INTEGER;
ALTER TABLE `host` CHANGE `subnet_id` `subnet_id` INTEGER;
ALTER TABLE `subnet` CHANGE `image_server_id` `image_server_id` INTEGER;
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}

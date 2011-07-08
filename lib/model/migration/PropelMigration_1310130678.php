<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1310130678.
 * Generated on 2011-07-08 15:11:18 by lezard
 */
class PropelMigration_1310130678
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
ALTER TABLE `command` CHANGE `std_out_file` `std_out_file` VARCHAR(200);
ALTER TABLE `command` CHANGE `std_err_file` `std_err_file` VARCHAR(200);
ALTER TABLE `command` CHANGE `exit_file` `exit_file` VARCHAR(200);
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
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;
ALTER TABLE `command` CHANGE `std_out_file` `std_out_file` VARCHAR(50);
ALTER TABLE `command` CHANGE `std_err_file` `std_err_file` VARCHAR(50);
ALTER TABLE `command` CHANGE `exit_file` `exit_file` VARCHAR(50);
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}

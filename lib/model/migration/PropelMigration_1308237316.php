<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1308237316.
 * Generated on 2011-06-16 17:15:16 by lezard
 */
class PropelMigration_1308237316
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
		return array ('propel' => 'ALTER TABLE `command` ADD(`exit_file` VARCHAR(50));');
	}

	/**
	 * Get the SQL statements for the Down migration
	 *
	 * @return array list of the SQL strings to execute for the Down migration
	 *               the keys being the datasources
	 */
	public function getDownSQL()
	{
		return array ('propel' => 'ALTER TABLE `command` DROP `exit_file`;');
	}

}

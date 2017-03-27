<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1308159007.
 * Generated on 2011-06-15 19:30:07 by lezard
 */
class PropelMigration_1308159007
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
ALTER TABLE `pxe_file` CHANGE `name` `description` VARCHAR(50);
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
ALTER TABLE `pxe_file` CHANGE `description` `name` VARCHAR(50);
',
);
	}

}

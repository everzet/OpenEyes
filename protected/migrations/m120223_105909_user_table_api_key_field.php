<?php

class m120223_105909_user_table_api_key_field extends CDbMigration
{
	public function up()
	{
		$this->addColumn('user','api_key','varchar(40) DEFAULT NULL');
	}

	public function down()
	{
		$this->dropColumn('user','api_key');
	}
}

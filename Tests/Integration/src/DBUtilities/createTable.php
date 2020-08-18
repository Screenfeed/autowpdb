<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBUtilities;

use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilitiesIntegration as DBUtilities;

/**
 * Tests for DBUtilities::create_table().
 *
 * @covers DBUtilities::create_table
 * @group  DBUtilities
 */
class Test_CreateTable extends TestCase {
	protected $drop_table = true;

	public function testShouldReturnTrue() {

		$result = DBUtilities::create_table(
			$this->table_name,
			$this->get_table_schema(),
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertTrue( $result );
		$this->assertCount( 0, $this->logs );
	}

	public function testShouldReturnFalseWhenDBError() {
		$error = "Error while creating the DB table {$this->table_name}: You have an error in your SQL syntax;";

		$result = DBUtilities::create_table(
			$this->table_name,
			'',
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertFalse( $result );
		$this->assertCount( 1, $this->logs );

		foreach ( $this->logs as $log ) {
			$this->assertStringStartsWith( $error, $log );
		}
	}

	public function testShouldFailWithoutLogging() {
		remove_filter( 'screenfeed_autowpdb_can_log', [ $this, 'return_true' ] );

		$result = DBUtilities::create_table(
			$this->table_name,
			'',
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertFalse( $result );
		$this->assertCount( 0, $this->logs );
	}

	private function get_table_schema(): string {
		return "
			file_id bigint(20) unsigned NOT NULL auto_increment,
			file_date datetime NOT NULL default '0000-00-00 00:00:00',
			path varchar(191) NOT NULL default '',
			mime_type varchar(100) NOT NULL default '',
			modified tinyint(1) unsigned NOT NULL default 0,
			width smallint(2) unsigned NOT NULL default 0,
			height smallint(2) unsigned NOT NULL default 0,
			file_size int(4) unsigned NOT NULL default 0,
			status varchar(20) default NULL,
			error varchar(255) default NULL,
			data longtext default NULL,
			PRIMARY KEY  (file_id),
			UNIQUE KEY path (path),
			KEY status (status),
			KEY modified (modified)";
	}
}

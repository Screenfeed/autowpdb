<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\TableUpgrader;

/**
 * Tests for TableUpgrader->upgrade_table().
 *
 * @covers TableUpgrader::upgrade_table
 * @group  TableUpgrader
 */
class Test_UpgradeTable extends TestCase {
	protected $drop_table = true;

	public function testShouldSetTableNotReady() {
		$error = "Error while creating the DB table {$this->table_name}: A table must have at least 1 column";

		$this->init(
			[
				'logger' => [ $this, 'log' ],
			],
			[
				'table_schema' => 'PRIMARY KEY  (file_id)',
			]
		);

		$this->upgrader->upgrade_table();

		$this->assertTableIsNotReady();
		$this->assertDbVersionIs( 0 );
		$this->assertCount( 1, $this->get_logs() );

		foreach ( $this->get_logs() as $log ) {
			$this->assertStringStartsWith( $error, $log );
		}

		$this->reset();
	}

	public function testShouldSetTableReady() {
		global $wpdb;

		// Create the table with only 2 columns.
		$schema = "
		file_id bigint(20) unsigned NOT NULL auto_increment,
		path varchar(191) NOT NULL default '',
		PRIMARY KEY  (file_id),
		UNIQUE KEY path (path)";
		$this->init(
			[],
			[
				'table_version' => 100,
				'table_schema'  => $schema,
			]
		);

		$this->upgrader->upgrade_table();

		$this->assertTableIsReady();
		$this->assertDbVersionIs();

		// Try to upgrade the table with all columns.
		$this->init();

		$this->upgrader->upgrade_table();

		$this->assertTableIsReady();
		$this->assertDbVersionIs();

		// Create an entry to make sure the new columns have been added.
		$table_name = $this->table_def->get_table_name();
		$wpdb->insert(
			$table_name,
			[ 'path' => 'foo' ],
			[ 'path' => '%s' ]
		);

		$result = $wpdb->get_row( "SELECT * FROM `$table_name` LIMIT 1" );

		$this->assertIsObject( $result );
		$this->assertObjectHasAttribute( 'mime_type', $result );

		$this->reset();
	}

	private function reset() {
		$this->deleteDbVersion();
		$this->resetTableReady();

		remove_filter( 'screenfeed_autowpdb_table_is_allowed_to_upgrade', [ $this, 'return_false' ] );
		remove_filter( 'screenfeed_autowpdb_table_is_allowed_to_upgrade', [ $this, 'return_true' ] );
	}
}

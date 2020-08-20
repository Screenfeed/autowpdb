<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBUtilities;

use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilitiesIntegration as DBUtilities;

/**
 * Tests for DBUtilities::delete_table().
 *
 * @covers DBUtilities::delete_table
 * @group  DBUtilities
 */
class Test_DeleteTable extends TestCase {
	protected $drop_table = true;

	public function testShouldReturnTrue() {
		$this->create_table();

		$result = DBUtilities::delete_table(
			$this->table_name,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertTrue( $result );
		$this->assertCount( 0, $this->get_logs() );
	}

	public function testShouldReturnFalse() {
		$error = "Deletion of the DB table {$this->table_name} failed.";

		$result = DBUtilities::delete_table(
			$this->table_name,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertFalse( $result );
		$this->assertCount( 1, $this->get_logs() );
		$this->assertContains( $error, $this->get_logs() );
	}

	public function testShouldFailWithoutLogging() {
		remove_filter( 'screenfeed_autowpdb_can_log', [ $this, 'return_true' ] );

		$result = DBUtilities::delete_table(
			$this->table_name,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertFalse( $result );
		$this->assertCount( 0, $this->get_logs() );
	}
}

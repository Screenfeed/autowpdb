<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBUtilities;

use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilitiesUnit;
use wpdb;

/**
 * Tests for DBUtilities::delete_table().
 *
 * @covers DBUtilities::delete_table
 * @group  DBUtilities
 */
class Test_DeleteTable extends TestCase {

	public function testShouldReturnTrue() {
		$this->createMocks( true );

		DBUtilitiesUnit::$mocks = [
			'table_exists' => false,
			'can_log'      => true,
		];

		$result = DBUtilitiesUnit::delete_table(
			$this->table_name,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertTrue( $result );
		$this->assertCount( 0, $this->logs );
	}

	public function testShouldReturnFalseWhenQueryFails() {
		$this->createMocks( false );

		DBUtilitiesUnit::$mocks = [
			'table_exists' => false,
			'can_log'      => true,
		];

		$result = DBUtilitiesUnit::delete_table(
			$this->table_name,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$error = sprintf( 'Deletion of the DB table %s failed.', $this->table_name );

		$this->assertFalse( $result );
		$this->assertContains( $error, $this->logs );
		$this->assertCount( 1, $this->logs );
	}

	public function testShouldReturnFalseWhenTableError() {
		$this->createMocks( true );

		DBUtilitiesUnit::$mocks = [
			'table_exists' => true,
			'can_log'      => true,
		];

		$result = DBUtilitiesUnit::delete_table(
			$this->table_name,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$error = sprintf( 'Deletion of the DB table %s failed.', $this->table_name );

		$this->assertFalse( $result );
		$this->assertContains( $error, $this->logs );
		$this->assertCount( 1, $this->logs );
	}

	public function testShouldFailWithoutLogging() {
		$this->createMocks( false );

		DBUtilitiesUnit::$mocks = [
			'table_exists' => false,
			'can_log'      => false,
		];

		$result = DBUtilitiesUnit::delete_table(
			$this->table_name,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertFalse( $result );
		$this->assertCount( 0, $this->logs );
	}

	public function createMocks( $result = true ) {
		global $wpdb;

		$wpdb = $this->getMockBuilder( wpdb::class )
			->setMethods( [ 'hide_errors', 'query' ] )
			->getMock();
		$wpdb
			->expects( $this->once() )
			->method( 'hide_errors' );
		$wpdb
			->expects( $this->once() )
			->method( 'query' )
			->with( "DROP TABLE `{$this->table_name}`" )
			->willReturn( $result );
	}
}

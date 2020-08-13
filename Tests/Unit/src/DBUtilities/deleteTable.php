<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBUtilities;

use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilities as MockDBUtilities;
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

		MockDBUtilities::$mocks = [
			'table_exists' => false,
			'can_log'      => true,
		];

		$result = MockDBUtilities::delete_table(
			$this->table_name,
			[
				'logger' => $this->logger,
			]
		);

		$this->assertTrue( $result );
		$this->assertCount( 0, MockDBUtilities::$logs );
	}

	public function testShouldReturnFalseWhenQueryFails() {
		$this->createMocks( false );

		MockDBUtilities::$mocks = [
			'table_exists' => false,
			'can_log'      => true,
		];

		$result = MockDBUtilities::delete_table(
			$this->table_name,
			[
				'logger' => $this->logger,
			]
		);

		$error = sprintf( 'Deletion of the DB table %s failed.', $this->table_name );

		$this->assertFalse( $result );
		$this->assertContains( $error, MockDBUtilities::$logs );
		$this->assertCount( 1, MockDBUtilities::$logs );
	}

	public function testShouldReturnFalseWhenTableError() {
		$this->createMocks( true );

		MockDBUtilities::$mocks = [
			'table_exists' => true,
			'can_log'      => true,
		];

		$result = MockDBUtilities::delete_table(
			$this->table_name,
			[
				'logger' => $this->logger,
			]
		);

		$error = sprintf( 'Deletion of the DB table %s failed.', $this->table_name );

		$this->assertFalse( $result );
		$this->assertContains( $error, MockDBUtilities::$logs );
		$this->assertCount( 1, MockDBUtilities::$logs );
	}

	public function testShouldFailWithoutLogging() {
		$this->createMocks( false );

		MockDBUtilities::$mocks = [
			'table_exists' => false,
			'can_log'      => false,
		];

		$result = MockDBUtilities::delete_table(
			$this->table_name,
			[
				'logger' => $this->logger,
			]
		);

		$this->assertFalse( $result );
		$this->assertCount( 0, MockDBUtilities::$logs );
	}

	public function createMocks( $result = true ) {
		global $wpdb;

		$wpdb = $this->getMockBuilder( wpdb::class )
			->setMethods( [ 'query' ] )
			->getMock();
		$wpdb
			->expects( $this->once() )
			->method( 'query' )
			->with( "DROP TABLE `{$this->table_name}`" )
			->willReturn( $result );
	}
}

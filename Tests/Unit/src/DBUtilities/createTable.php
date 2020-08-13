<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBUtilities;

use Brain\Monkey\Functions;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilities as MockDBUtilities;
use wpdb;

/**
 * Tests for DBUtilities::create_table().
 *
 * @covers DBUtilities::create_table
 * @group  DBUtilities
 */
class Test_CreateTable extends TestCase {
	protected $schema_query    = 'schema_query';
	protected $charset_collate = 'charset_collate';

	public function testShouldReturnTrue() {
		$this->createMocks();

		MockDBUtilities::$mocks = [
			'table_exists' => true,
			'can_log'      => true,
		];

		$result = MockDBUtilities::create_table(
			$this->table_name,
			$this->schema_query,
			[
				'logger' => $this->logger,
			]
		);

		$this->assertTrue( $result );
		$this->assertCount( 0, MockDBUtilities::$logs );
	}

	public function testShouldReturnFalseWhenDBError() {
		$db_error = 'You messed it up!';

		$this->createMocks( $db_error );

		MockDBUtilities::$mocks = [
			'table_exists' => true,
			'can_log'      => true,
		];

		$result = MockDBUtilities::create_table(
			$this->table_name,
			$this->schema_query,
			[
				'logger' => $this->logger,
			]
		);

		$error = sprintf( 'Error while creating the DB table %s: %s', $this->table_name, $db_error );

		$this->assertFalse( $result );
		$this->assertContains( $error, MockDBUtilities::$logs );
		$this->assertCount( 1, MockDBUtilities::$logs );
	}

	public function testShouldReturnFalseWhenTableError() {
		$this->createMocks();

		MockDBUtilities::$mocks = [
			'table_exists' => false,
			'can_log'      => true,
		];

		$result = MockDBUtilities::create_table(
			$this->table_name,
			$this->schema_query,
			[
				'logger' => $this->logger,
			]
		);

		$error = sprintf( 'Creation of the DB table %s failed.', $this->table_name );

		$this->assertFalse( $result );
		$this->assertContains( $error, MockDBUtilities::$logs );
		$this->assertCount( 1, MockDBUtilities::$logs );
	}

	public function testShouldFailWithoutLogging() {
		$db_error = 'You messed it up!';

		$this->createMocks( $db_error );

		MockDBUtilities::$mocks = [
			'table_exists' => true,
			'can_log'      => false,
		];

		$result = MockDBUtilities::create_table(
			$this->table_name,
			$this->schema_query,
			[
				'logger' => $this->logger,
			]
		);

		$this->assertFalse( $result );
		$this->assertCount( 0, MockDBUtilities::$logs );
	}

	public function createMocks( $db_error = '' ) {
		global $wpdb;

		$wpdb = $this->getMockBuilder( wpdb::class )
			->setMethods( [ 'hide_errors', 'get_charset_collate' ] )
			->getMock();
		$wpdb
			->expects( $this->once() )
			->method( 'hide_errors' );
		$wpdb
			->expects( $this->once() )
			->method( 'get_charset_collate' )
			->with()
			->willReturn( $this->charset_collate );

		if ( ! empty( $db_error ) ) {
			$wpdb->last_error = $db_error;
		}

		Functions\expect( 'dbDelta' )
			->once()
			->with( "CREATE TABLE `{$this->table_name}` ({$this->schema_query}) {$this->charset_collate};" );
	}
}

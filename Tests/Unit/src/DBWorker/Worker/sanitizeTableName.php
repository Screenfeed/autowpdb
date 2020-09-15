<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBWorker\Worker;

use Brain\Monkey\Functions;
use Screenfeed\AutoWPDB\DBWorker\Worker;

/**
 * Tests for Worker::sanitize_table_name().
 *
 * @covers Worker::sanitize_table_name
 * @group  Worker
 */
class Test_SanitizeTableName extends TestCase {

	public function testShouldReturnSanitizedTableName() {
		$this->createMocks();

		$worker = new Worker();
		$result = $worker->sanitize_table_name( ' _tâBLé---_nàmè_0- ' );

		$this->assertSame( 'table_name_0', $result );

		$result = $worker->sanitize_table_name( ' 0 ' );

		$this->assertSame( '0', $result );
	}

	public function testShouldReturnNullWhenOnlyInvalidCharacters() {
		$this->createMocks();

		$result = ( new Worker() )->sanitize_table_name( '&%£' );

		$this->assertNull( $result );
	}

	public function createMocks() {
		Functions\expect( 'remove_accents' )
			->andReturnUsing(
				function( $string ) {
					return str_replace( [ 'â', 'à', 'é', 'è', 'î', 'ô', 'ù' ], [ 'a', 'a', 'e', 'e', 'i', 'o', 'u' ], $string );
				}
			);
		Functions\expect( 'sanitize_key' )
			->andReturnUsing(
				function( $string ) {
					return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( $string ) );
				}
			);
	}
}

<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBWorker\Worker;

use Brain\Monkey\Functions;
use Mockery;
use Screenfeed\AutoWPDB\DBWorker\Worker;

/**
 * Tests for Worker->prepare_values_list().
 *
 * @covers Worker::prepare_values_list
 * @group  Worker
 */
class Test_PrepareValuesList extends TestCase {

	public function testShouldReturnPreparedList() {
		$worker = $this->createMocks();

		$result = $worker->prepare_values_list(
			[
				2,
				'3',
				'',
				"string with ' \" quotes",
				'string',
			]
		);

		$this->assertSame( "2,3,'','string with \' \\\" quotes','string'", $result );
	}

	public function createMocks() {
		Functions\expect( 'esc_sql' )
			->once()
			->with( Mockery::type( 'array' ) )
			->andReturnUsing(
				function ( $values ) {
					return array_map( 'addslashes', $values );
				}
			);

		$worker = $this->getMockBuilder( Worker::class )
			->setMethods( [ 'quote_string' ] )
			->getMock();
		$worker
			->expects( $this->any() )
			->method( 'quote_string' )
			->withAnyParameters()
			->willReturnCallback(
				function ( $value ) {
					switch ( $value ) {
						case '2':
						case '3':
							$return = $value;
							break;
						default:
							$return = "'" . $value . "'";
							break;
					}
					return $return;
				}
			);

		return $worker;
	}
}

<?php
/**
 * Allows to mock static methods.
 *
 * HOW TO USE:
 * Let's say you want to test MyClass::barbaz(), but it uses static::foobar() internally.
 * Then you need to mock MyClass::foobar(), and want it to return true for example.
 *
 * Create a new class extending "MyClass", and overload all its static methods:
 * class MyClassMock extends MyClass {
 *     use StaticMethodMocker;
 *
 *     public static function foobar( $foo = '', $bar = [] ) {
 *         return static::maybe_mock_static( __FUNCTION__, $foo, $bar );
 *     }
 * }
 *
 * Then in your test:
 * MyClassMock::$mocks = [
 *     'foobar' => true,
 * ];
 * $result = MyClassMock::barbaz();
 *
 * @package Screenfeed\AutoWPDB\Tests\Unit
 */

namespace Screenfeed\AutoWPDB\Tests\Unit;

trait StaticMethodMocker {
	public static $mocks = [];

	protected final static function maybe_mock_static( $method ) {
		if ( ! isset( static::$mocks[ $method ] ) ) {
			$args = func_get_args();
			array_shift( $args );
			return call_user_func_array( "parent::$method", $args );
		}

		if ( ! is_callable( static::$mocks[ $method ] ) ) {
			return static::$mocks[ $method ];
		}

		$args = func_get_args();
		array_shift( $args );
		return call_user_func_array( static::$mocks[ $method ], $args );
	}
}

<?php

namespace Screenfeed\AutoWPDB\Tests;

use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;

trait TestCaseTrait {

	/**
	 * Reset the value of a private/protected property to null.
	 *
	 * @param string|object $class    Class name for a static property, or instance for an instance property.
	 * @param string        $property Property name for which to gain access.
	 *
	 * @return mixed                  The previous value of the property.
	 * @throws ReflectionException    Throws an exception if property does not exist.
	 *
	 */
	protected function resetPropertyValue( $class, $property ) {
		return $this->setPropertyValue( $class, $property, null );
	}

	/**
	 * Set the value of a private/protected property.
	 *
	 * @param string|object $class    Class name for a static property, or instance for an instance property.
	 * @param string        $property Property name for which to gain access.
	 * @param mixed         $value    The value to set to the property.
	 *
	 * @return mixed                  The previous value of the property.
	 * @throws ReflectionException    Throws an exception if property does not exist.
	 *
	 */
	protected function setPropertyValue( $class, $property, $value ) {
		$ref = $this->get_reflective_property( $class, $property );

		if ( is_object( $class ) ) {
			$previous = $ref->getValue( $class );
			// Instance property.
			$ref->setValue( $class, $value );
		} else {
			$previous = $ref->getValue();
			// Static property.
			$ref->setValue( $value );
		}

		return $previous;
	}

	/**
	 * Get the value of a private/protected property.
	 *
	 * @param string|object $class    Class name for a static property, or instance for an instance property.
	 * @param string        $property Property name for which to gain access.
	 *
	 * @return mixed
	 * @throws ReflectionException    Throws an exception if property does not exist.
	 *
	 */
	protected function getPropertyValue( $class, $property ) {
		$ref = $this->get_reflective_property( $class, $property );

		return $ref->getValue( $class );
	}

	/**
	 * Invoke a private/protected method.
	 *
	 * @param string|object $class  Class name for a static method, or instance for an instance method.
	 * @param string        $method Method name for which to gain access.
	 *
	 * @return mixed                The method result.
	 * @throws ReflectionException  Throws an exception upon failure.
	 *
	 */
	protected function invokeMethod( $class, $method ) {
		if ( is_string( $class ) ) {
			$class_name = $class;
		} else {
			$class_name = get_class( $class );
		}

		$method = $this->get_reflective_method( $class_name, $method );

		return $method->invoke( $class );
	}

	/** ----------------------------------------------------------------------------------------- */
	/** REFLECTIONS ============================================================================= */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Get reflective access to a private/protected method.
	 *
	 * @param string|object $class  Class name for a static method, or instance for an instance method.
	 * @param string        $method Method name for which to gain access.
	 *
	 * @return ReflectionMethod
	 * @throws ReflectionException Throws an exception if method does not exist.
	 *
	 */
	protected function get_reflective_method( $class, $method ) {
		$method = new ReflectionMethod( $class, $method );
		$method->setAccessible( true );

		return $method;
	}

	/**
	 * Get reflective access to a private/protected property.
	 *
	 * @param string|object $class    Class name for a static property, or instance for an instance property.
	 * @param string        $property Property name for which to gain access.
	 *
	 * @return ReflectionProperty
	 * @throws ReflectionException Throws an exception if property does not exist.
	 *
	 */
	protected function get_reflective_property( $class, $property ) {
		$property = new ReflectionProperty( $class, $property );
		$property->setAccessible( true );

		return $property;
	}

	/**
	 * Set the value of a private/protected property.
	 *
	 * @param string|object $class    Class name for a static property, or instance for an instance property.
	 * @param string        $property Property name for which to gain access.
	 * @param mixed         $value    The value to set for the property.
	 *
	 * @return ReflectionProperty
	 * @throws ReflectionException Throws an exception if property does not exist.
	 *
	 */
	protected function set_reflective_property( $class, $property, $value ) {
		$property = $this->get_reflective_property( $class, $property );

		if ( is_object( $class ) ) {
			// Instance property.
			$ref->setValue( $class, $value );
		} else {
			// Static property.
			$ref->setValue( $value );
		}

		$property->setAccessible( false );

		return $property;
	}
}

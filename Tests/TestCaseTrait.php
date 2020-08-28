<?php

namespace Screenfeed\AutoWPDB\Tests;

use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;

trait TestCaseTrait {

	/**
	 * Reset the value of a private/protected property.
	 *
	 * @param string        $property Property name for which to gain access.
	 * @param string|object $class    Class name for a static property, or instance for an instance property.
	 *
	 * @return mixed                  The previous value of the property.
	 * @throws ReflectionException    Throws an exception if property does not exist.
	 *
	 */
	protected function resetPropertyValue( $property, $class ) {
		return $this->setPropertyValue( $property, $class, null );
	}

	/**
	 * Set the value of a private/protected property.
	 *
	 * @param string        $property Property name for which to gain access.
	 * @param string|object $class    Class name for a static property, or instance for an instance property.
	 * @param mixed         $value    The value to set to the property.
	 *
	 * @return mixed                  The previous value of the property.
	 * @throws ReflectionException    Throws an exception if property does not exist.
	 *
	 */
	protected function setPropertyValue( $property, $class, $value ) {
		$ref = $this->get_reflective_property( $property, $class );

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
	 * @param string        $property Property name for which to gain access.
	 * @param string|object $class    Class name for a static property, or instance for an instance property.
	 *
	 * @return mixed
	 * @throws ReflectionException    Throws an exception if property does not exist.
	 *
	 */
	protected function getPropertyValue( $property, $class ) {
		$ref = $this->get_reflective_property( $property, $class );

		return $ref->getValue( $class );
	}

	/**
	 * Invoke a private/protected method.
	 *
	 * @param string        $method Method name for which to gain access.
	 * @param object|string $class  An instance of the target class, or its name.
	 *
	 * @return ReflectionMethod
	 * @throws ReflectionException Throws an exception if method does not exist.
	 *
	 */
	protected function invokeMethod( $method, $class ) {
		if ( is_string( $class ) ) {
			$class_name = $class;
		} else {
			$class_name = get_class( $class );
		}

		$method = $this->get_reflective_method( $method, $class_name );
		$method->invoke( $class );

		return $method;
	}

	/** ----------------------------------------------------------------------------------------- */
	/** REFLECTIONS ============================================================================= */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Get reflective access to a private/protected method.
	 *
	 * @param string $method_name Method name for which to gain access.
	 * @param string $class_name  Name of the target class.
	 *
	 * @return ReflectionMethod
	 * @throws ReflectionException Throws an exception if method does not exist.
	 *
	 */
	protected function get_reflective_method( $method_name, $class_name ) {
		$method = new ReflectionMethod( $class_name, $method_name );
		$method->setAccessible( true );

		return $method;
	}

	/**
	 * Get reflective access to a private/protected property.
	 *
	 * @param string       $property_name Property name for which to gain access.
	 * @param string|mixed $class_name    Class name or instance.
	 *
	 * @return ReflectionProperty
	 * @throws ReflectionException Throws an exception if property does not exist.
	 *
	 */
	protected function get_reflective_property( $property_name, $class_name ) {
		$property = new ReflectionProperty( $class_name, $property_name );
		$property->setAccessible( true );

		return $property;
	}

	/**
	 * Set the value of a private/protected property.
	 *
	 * @param mixed  $value    The value to set for the property.
	 * @param string $property Property name for which to gain access.
	 * @param mixed  $instance Instance of the target object.
	 *
	 * @return ReflectionProperty
	 * @throws ReflectionException Throws an exception if property does not exist.
	 *
	 */
	protected function set_reflective_property( $value, $property, $instance ) {
		$property = $this->get_reflective_property( $property, $instance );
		$property->setValue( $instance, $value );
		$property->setAccessible( false );

		return $property;
	}
}

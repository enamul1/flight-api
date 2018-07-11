<?php

namespace App\FlightProviders;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FlightProviderFactory
{
    /**
     * factory for instantiating provider
     *
     * @param string $providerName (e.g. amadeus)
     * @param string $type action type (e.g. search)
     * @return mixed
     */
    public static function get($providerName, $type)
    {
        $className = 'App\FlightProviders\\' . ucwords($providerName) . '\\' . ucwords($type);

        if (class_exists($className)) {

            // dynamic object creation with dependency injection
            $class = new \ReflectionClass($className);
            $constructor = $class->getConstructor();

            // return new object now, if it has no constructor
            if (is_null($constructor)) {
                return $class->newInstance();
            }

            $params = $constructor->getParameters();
            $args = [];
            foreach ($params as $param) {
                try {
                    $args[] = \App::make($param->getClass()->name);
                } catch (\Exception $e) {
                    throw new NotFoundHttpException('Flight Provider can not be instantiated.');
                }
            }

            return $class->newInstanceArgs($args);
        } else {
            throw new NotFoundHttpException('Flight Provider not found.');
        }
    }
}

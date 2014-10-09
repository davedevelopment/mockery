<?php

namespace Mockery\TestDouble;

interface Spy extends Stub
{
    /**
     * @param   string  $method     The method that is expected to be have been called
     *
     * @return MethodCallVerification
     */
    function shouldHaveReceived($method);

    /**
     * @param   string  $method     The method that is expected to not have been called
     * @param   array   $args       The arguments that were expected to not have been passed (optional)
     *
     * @return MethodCallVerification
     */
    function shouldNotHaveReceived($method, array $args = array())
}

interface MethodCallVerification extends MethodExpectation
{
    function once();
    function twice();

    function with($args);
}


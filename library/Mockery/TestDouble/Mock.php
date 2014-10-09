<?php

namespace Mockery\TestDouble;

interface Mock extends Stub
{
    /**
     * @param   string  $method     The method that is expected to be called
     *
     * @return MethodExpectation
     */
    function shouldReceive($method);

    /**
     * @param   string  $method     The method that is expected to be called
     *
     * @return MethodExpectation
     */
    function shouldNotReceive($method);
}

interface MethodExpectation
{
    function zeroOrMoreTimes(); // not to be included

    function once();
    function twice();

    function with($args);

    // yada yada
}


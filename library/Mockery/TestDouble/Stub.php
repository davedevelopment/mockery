<?php

namespace Mockery\TestDouble;

interface Stub extends TestDouble
{
    /**
     * @param  string   $method   The method to stub
     * @return StubMethod
     */
    function stub($method);
}

/** 
 * @todo decide on andReturn/toReturn/willReturn 
 */
interface StubMethod
{

}

<?php
/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 * @package    Mockery
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace Mockery\VCR;

class Recordable implements \Mockery\VCR\RecordableInterface
{
    private $_instance;
    private $_cassette;

    public function __construct($instance, \Mockery\VCR\CassetteStack $cassetteStack)
    {
        $this->_instance = $instance;
        $this->_cassetteStack = $cassetteStack;
    }

    public function __call($method, $args)
    {
        return $this->_handleMethodCall($method, ...$args);
    }

    public function _mockery_handleMethodCall($method, ...$args)
    {
        $id = get_parent_class()."::".$method."[".md5(serialize($args))."]";

        return $this->_cassetteStack->playOrRecord($id, function () use ($method, $args) {
            return $this->_instance->$method(...$args);
        });
    }

}

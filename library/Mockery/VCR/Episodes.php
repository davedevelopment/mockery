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

class Episodes
{
    private $episodes = [];

    public function __construct(array $episodes = [])
    {
        foreach ($episodes as $episode) {
            if (!$episode instanceof Episode) {
                throw new InvalidArgumentException("All episodes must be instances of Episode");
            }
        }
        $this->episodes = $episodes;
    }

    public function has($id)
    {
        return $this->get($id) !== null;
    }

    public function merge(Episodes $second): Episodes
    {
        return new static(array_merge($this->episodes, $second->episodes));
    }

    public function add(Episode $episode): Episodes
    {
        return $this->merge(new static([$episode]));
    }

    public function get($id)
    {
        foreach ($this->episodes as $episode) {
            if ($episode->id() == $id) {
                return $episode;
            }
        }

        return null;
    }

    public function map(callable $callable)
    {
        $values = [];
        foreach ($this->episodes as $episode) {
            $values[] = $callable($episode);
        }

        return $values;
    }
}

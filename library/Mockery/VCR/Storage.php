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

class Storage 
{
    private $dir;

    public function __construct($dir = null)
    {
        $this->dir = $dir ?? "/tmp/mockery_vcr";
    }

    public function get(string $filename)
    {
        if (file_exists($this->absolutePath($filename))) {
            return file_get_contents($this->absolutePath($filename));
        }
        
        return null;
    }

    public function put(string $filename, string $data)
    {
        $absolutePath = $this->absolutePath($filename);

        if (!is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0777, true);
        }

        return file_put_contents($absolutePath, $data);
    }

    public function absolutePath(string $filename)
    {
        return rtrim($this->dir, "/")."/".ltrim($filename, "/");
    }
}


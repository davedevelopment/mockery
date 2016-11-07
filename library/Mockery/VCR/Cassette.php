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

class Cassette
{
    private $name;
    private $serializer;
    private $newEpisodes;
    private $previousEpisodes;
    private $rawBytes;
    private $deserialisedData;

    public function __construct($name, Storage $storage = null, Serializer $serializer = null)
    {
        $this->name = $name;
        $this->storage = $storage ?? new Storage;
        $this->serializer = $serializer ?? new PHPSerializer;
    }

    public function has($episodeId)
    {
        return $this->episodes()->has($episodeId);
    }

    public function play($episodeId)
    {
        return $this->episodes()->get($episodeId)->output();
    }

    public function record($episodeId, callable $callback)
    {
        $output = $callback();
        $this->newEpisodes = $this->newEpisodes()->add(new Episode($episodeId, $output));

        return $output;
    }

    public function eject()
    {
        // serialise each episode        
        $episodes = $this->episodes()->map(function ($episode) {
            return [$episode->id(), $episode->output()];
        });

        $serialisedData = $this->serializer->serialize([
            'episodes' => $episodes,
            'version' => 'dev',
        ]);

        $this->storage->put($this->storageKey(), $serialisedData);
    }

    private function episodes(): Episodes
    {
        return $this->previousEpisodes()->merge($this->newEpisodes());
    }

    private function previousEpisodes(): Episodes
    {
        if (!$this->previousEpisodes) {
            if ($this->rawBytes() != "") {
                $this->previousEpisodes = new Episodes(array_map(function ($tuple) {
                    return new Episode($tuple[0], $tuple[1]);
                }, $this->deserialisedData()['episodes']));
            } else {
                $this->previousEpisodes = new Episodes();
            }
        }

        return $this->previousEpisodes;
    }

    private function newEpisodes(): Episodes
    {
        if (!$this->newEpisodes) {
            $this->newEpisodes = new Episodes();
        }

        return $this->newEpisodes;
    }

    private function rawBytes()
    {
        if (null === $this->rawBytes) {
            $this->rawBytes = $this->storage->get($this->storageKey());
        }

        return $this->rawBytes;
    }

    private function storageKey()
    {
        return $this->name.".".$this->serializer->extension();
    }

    private function deserialisedData(): array
    {
        if (null === $this->deserialisedData) {
            $this->deserialisedData = $this->serializer->unserialize($this->rawBytes());
        }

        return $this->deserialisedData;
    }
}


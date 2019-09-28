<?php

namespace Anper\Jsonbox;

/**
 * Class Uri
 * @package Anper\Jsonbox
 */
class Uri
{
    /**
     * @var array
     */
    protected $path = [];

    /**
     * @var array
     */
    protected $query = [];

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->addPath($path);
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function addPath(string $path): self
    {
        $this->path[] = \trim($path, '/');

        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return \implode('/', $this->path);
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return \http_build_query($this->query);
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function withPath(string $path): self
    {
        $clone = clone $this;
        $clone->addPath($path);

        return $clone;
    }

    /**
     * @param array $query
     *
     * @return $this
     */
    public function withQuery(array $query): self
    {
        $clone = clone $this;
        $clone->query = $query;

        return $clone;
    }
}

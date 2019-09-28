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
     * @param array $query
     */
    public function __construct(string $path, array $query = [])
    {
        $this->addPath($path);
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return '/' . \implode('/', $this->path);
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
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

    /**
     * @param string $path
     *
     * @return $this
     */
    protected function addPath(string $path): self
    {
        $this->path[] = \trim($path, '/');

        return $this;
    }
}

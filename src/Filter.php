<?php

namespace Anper\Jsonbox;

/**
 * Class QueryBuilder
 * @package Anper\Jsonbox
 */
class Filter
{
    /**
     * @var array
     */
    protected $query = [];

    /**
     * @var int|null
     */
    protected $limit;

    /**
     * @var int|null
     */
    protected $skip;

    /**
     * @var string|null
     */
    protected $sort;

    /**
     * @param string $field
     * @param string $value
     *
     * @return $this
     */
    public function equalTo(string $field, string $value): self
    {
        return $this->where($field, $value, ':');
    }

    /**
     * @param string $field
     * @param int $value
     *
     * @return $this
     */
    public function greaterThan(string $field, int $value): self
    {
        return $this->where($field, (string) $value, ':>');
    }

    /**
     * @param string $field
     * @param int $value
     *
     * @return $this
     */
    public function greaterThanOrEqual(string $field, int $value): self
    {
        return $this->where($field, (string) $value, ':>=');
    }

    /**
     * @param string $field
     * @param int $value
     *
     * @return $this
     */
    public function lessThan(string $field, int $value): self
    {
        return $this->where($field, (string) $value, ':<');
    }

    /**
     * @param string $field
     * @param int $value
     *
     * @return $this
     */
    public function lessThanOrEqual(string $field, int $value): self
    {
        return $this->where($field, (string) $value, ':<=');
    }

    /**
     * @param string $field
     * @param string $value
     * @param string $op
     *
     * @return $this
     */
    public function where(string $field, string $value, string $op): self
    {
        $this->query[] = $field . $op . $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function clear(): self
    {
        $this->query = [];

        return $this;
    }

    /**
     * @param int|null $limit
     *
     * @return $this
     */
    public function limit(?int $limit): self
    {
        $this->limit = $limit === null
            ? null
            : \max(1, $limit);

        return $this;
    }

    /**
     * @param int|null $skip
     *
     * @return $this
     */
    public function skip(?int $skip): self
    {
        $this->skip = $skip === null
            ? null
            : \max(0, $skip);

        return $this;
    }

    /**
     * @param string|null $field
     *
     * @return $this
     */
    public function sort(?string $field): self
    {
        $this->sort = $field;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $query = [];

        if ($this->query) {
            $query['q'] = \implode(',', $this->query);
        }

        if ($this->limit !== null) {
            $query['limit'] = $this->limit;
        }

        if ($this->skip !== null) {
            $query['skip'] = $this->skip;
        }

        if ($this->sort !== null) {
            $query['sort'] = $this->sort;
        }

        return $query;
    }

    public function __toString()
    {
        return \http_build_query($this->toArray());
    }
}

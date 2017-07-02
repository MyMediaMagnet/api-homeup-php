<?php namespace HomeUp\Api;


class Query
{

    private $query = [];
    private $homeup;

    /**
     * Query constructor.
     * @param HomeUp $homeup
     */
    public function __construct(HomeUp $homeup)
    {
        $this->homeup = $homeup;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @param string $boolean
     * @return $this
     */
    public function where($column, $operator, $value, $boolean = 'and')
    {
        $this->query[] = [$column, $operator, $value];

        return $this;
    }

    /**
     * @param int $limit
     * @return \Psr\Http\Message\StreamInterface
     */
    public function get($limit = 10)
    {
        return Request::send($this->homeup->getBaseUrl() . '/api/v1/listings/query', $this->query, $this->homeup);
    }
}
<?php namespace HomeUp\Api;


use Closure;

class Query
{

    private $query = [];
    private $limit = 10;
    private $homeup;
    private $order_by = "id";
    private $order_direction = "DESC";

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
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        // If the columns is actually a Closure instance, we will assume the developer
        // wants to begin a nested where statement which is wrapped in parenthesis.
        // We'll add that Closure to the query then return back out immediately.
        if ($column instanceof Closure) {
            return $this->whereNested($column, $boolean);
        }

        $type = "Basic";
        $this->query[] = compact('type', 'column', 'operator', 'value', 'boolean');

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @return $this
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'or');
    }

    /**
     * Add a nested where statement to the query.
     *
     * @param  \Closure $callback
     * @param  string $boolean
     * @return Query
     */
    public function whereNested(Closure $callback, $boolean = 'and')
    {
        call_user_func($callback, $query = new static($this->homeup));

        return $this->addNestedWhereQuery($query, $boolean);
    }

    /**
     * Add another query builder as a nested where to the query builder.
     *
     * @param  $query
     * @param  string  $boolean
     * @return $this
     */
    public function addNestedWhereQuery($query, $boolean = 'and')
    {
        if (count($query->query)) {
            $type = 'Nested';

            $query = $query->query;

            $this->query[] = compact('type', 'query', 'boolean');
        }

        return $this;
    }

    /**
     * @param $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param $order_by
     * @param $direction
     * @return $this
     */
    public function orderBy($order_by, $direction)
    {
        $this->order_by = $order_by;
        $this->order_direction = $direction;

        return $this;
    }

    /**
     * @return \Psr\Http\Message\StreamInterface
     */
    public function get()
    {
        $data = [
            'query' => $this->query,
            'limit' => $this->limit,
            'order_by' => $this->order_by,
            'order_direction' => $this->order_direction
        ];
        return Request::send($this->homeup->getBaseUrl() . '/api/v1/listings/query', $data, $this->homeup);
    }

    /**
     * @return \Psr\Http\Message\StreamInterface
     */
    public function count()
    {
        $data = [
            'query' => $this->query,
        ];
        return Request::send($this->homeup->getBaseUrl() . '/api/v1/listings/count', $data, $this->homeup);
    }
}
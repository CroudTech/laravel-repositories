<?php

namespace Croud\Repositories;

use \Croud\Repositories\Contracts\RepositoryContract;
use \Illuminate\Contracts\Container\Container as ContainerContract;
use \Illuminate\Database\Eloquent\Builder as QueryBuilder;
use \Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements RepositoryContract
{
    /**
     * DI Container
     *
     * @var ContainerContract
     */
    private $container;

    /**
     * The query from this repositories Model
     *
     * @var \Illuminate\Database\Query\Builder
     */
    private $query;

    /**
     * Pass in the DI container
     *
     * @method __construct
     * @param  ContainerContract $container The DI container
     */
    public function __construct(ContainerContract $container)
    {
        $this->container = $container;
        $this->init();
    }

    /**
     * Overload in subclasses to set the model class for this repository
     *
     * @method getModelName
     * @return string
     */
    abstract public function getModelName() : string;

    /**
     * Get all records that match the current criteria
     *
     * @method all
     * @param  [type] $columns [description]
     * @return [type]          [description]
     */
    public function all($columns = ['*']) : \Illuminate\Support\Collection
    {
        return $this->query->get($columns);
    }

    /**
     * Create and return the model object
     *
     * @method create
     * @param  array  $data array
     * @return Model
     */
    public function create(array $data) : Model
    {
        $class = $this->getModelName();
        return $class::create($data);
    }

    public function delete($id)
    {

    }

    public function find($id, $columns = ['*'])
    {

    }

    public function findBy($field, $value, $columns = ['*'])
    {

    }

    public function paginate($perPage = 20, $columns = ['*'])
    {

    }

    public function update(array $data, $id)
    {

    }

    /**
     * Initialise the repository
     *
     * @method init
     */
    protected function init()
    {
        $this->makeQuery();
    }

    /**
     * [getQuery description]
     * @method getQuery
     * @return [type]   [description]
     */
    protected function makeQuery() : QueryBuilder
    {
        $model = $this->container->make($this->getModelName());

        if (!$model instanceof Model) {
            throw new RepositoryException("Class {$this->model()} must be an instance of " . Model::class);
        }

        return $this->query = $model->newQuery();
    }

    /**
     * Get the query object
     *
     * @method query
     * @return QueryBuilder
     */
    public function query() : QueryBuilder
    {
        return $this->query;
    }

    /**
     * Generate a new query object removing all existing constraints
     *
     * @method clearQuery
     * @return QueryBuilder
     */
    public function clearQuery() : QueryBuilder
    {
        return $this->makeQuery();
    }
}

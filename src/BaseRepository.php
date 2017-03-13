<?php

namespace CroudTech\Repositories;

use \CroudTech\Repositories\Contracts\RepositoryContract;
use \CroudTech\Repositories\Contracts\TransformerContract;
use \Illuminate\Contracts\Container\Container as ContainerContract;
use \Illuminate\Database\Eloquent\Builder as QueryBuilder;
use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Pagination\AbstractPaginator as Paginator;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use \League\Fractal\Resource\ResourceInterface;

abstract class BaseRepository implements RepositoryContract
{
    /**
     * DI Container
     *
     * @var ContainerContract
     */
    private $container;

    /**
     * Transformer
     *
     * @var TransformerContract
     */
    private $transformer;

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
    public function __construct(ContainerContract $container, TransformerContract $transformer)
    {
        $this->container = $container;
        $this->setTransformer($transformer);
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
        $model_class = $this->getModelName();
        return $model_class::create($data);
    }

    /**
     * Find a record by it's ID
     *
     * @method find
     * @param  integer
     * @return Model | null
     */
    public function find($id)
    {
        $model_class = $this->getModelName();
        return $model_class::find($id);
    }

    /**
     * Find a record by a custom field
     *
     * @method findBy
     * @param  string $field   The field to search by
     * @param  mixed $value    The field value
     * @return Model | null
     */
    public function findBy($field, $value)
    {
        $model_class = $this->getModelName();
        return $model_class::where($field, $value)->first();
    }

    /**
     * Update a record
     *
     * @method update
     * @param  $id          The ID of the record to update
     * @return boolean
     * @throws ModelNotFoundException
     */
    public function update($id, array $data)
    {
        if ($record = $this->find($id)) {
            return $record->update($data);
        } else {
            $model_class = $this->getModelName();
            throw new ModelNotFoundException('Model not found for ID ' . $id . ' on table ' . (new $model_class)->getTable());
        }
    }

    /**
     * Delete a record by ID
     *
     * @method delete
     * @param  integer $id The ID of the record
     * @return boolean
     */
    public function delete($id)
    {
        return $this->find($id)->delete();
    }

    /**
     * Standard Paginator
     *
     * @method paginate
     * @param  integer  $perPage The number of items per page
     * @return [type]            [description]
     */
    public function paginate($perPage = 20) : Paginator
    {
        return $this->query()->paginate($perPage);
    }

    /**
     * Simple Paginator
     *
     * @method simplePaginate
     * @param  integer  $perPage The number of items per page
     * @return [type]            [description]
     */
    public function simplePaginate($perPage = 20) : Paginator
    {
        return $this->query()->simplePaginate($perPage);
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

    /**
     * Get the injected transformer object
     *
     * @method getTransformer
     * @return TransformerContract
     */
    public function getTransformer() : TransformerContract
    {
        return $this->transformer;
    }

    /**
     * Set the transformer object
     *
     * @method setTransformer
     */
    public function setTransformer(TransformerContract  $transformer)
    {
        $this->transformer = $transformer;
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
}

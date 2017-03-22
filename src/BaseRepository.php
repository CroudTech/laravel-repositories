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
    protected $container;

    /**
     * Transformer
     *
     * @var TransformerContract
     */
    protected $transformer;

    /**
     * The query from this repositories Model
     *
     * @var \Illuminate\Database\Query\Builder
     */
    protected $query;

    /**
     * Pass in the DI container
     *
     * @method __construct
     * @param  ContainerContract        $container                  The DI container
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
     * @return [type]          [description]
     */
    public function all() : \Illuminate\Support\Collection
    {
        return $this->query->get();
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
     * Create and return the model object
     *
     * @method create
     * @param  array  $data array
     * @return Model
     */
    public function create(array $data) : Model
    {
        $model_class = $this->getModelName();
        $data = $this->preCreate($data);
        return $this->postCreate($data, $model_class::create($data));
    }

    /**
     * Callback to modify data before creation
     *
     * @method preCreate
     * @return $data
     */
    protected function preCreate($data) : array
    {
        return $data;
    }

    /**
     * Callback to modify the created model after creation
     *
     * @method postCreate
     * @param  array     $data       The data passed to the create method
     * @param  Model     $return_var The created model
     * @return Model                 Return the created model
     */
    protected function postCreate($data, Model $record) : Model
    {
        return $record;
    }

    /**
     * Update a record
     *
     * @method update
     * @param  $id          The ID of the record to update
     * @return boolean
     * @throws ModelNotFoundException
     */
    public function update($id, array $data) : bool
    {
        if ($record = $this->find($id)) {
            $data = $this->preUpdate($data, $id, $record);
            return $this->postUpdate($data, $id, $record->update($data), $record);
        } else {
            $this->throwModelNotFoundException($id);
        }
    }

    /**
     * Callback to modify data before creation
     *
     * @method preCreate
     * @return $data
     */
    protected function preUpdate(array $data, $id, Model $record) : array
    {
        return $data;
    }

    /**
     * Callback to modify the created model after creation
     *
     * @method postCreate
     * @param  array     $data       The data passed to the create method
     * @param  Model     $return_var The created model
     * @return Model                 Return the created model
     */
    protected function postUpdate($data, $id, $return_var, Model $record) : bool
    {
        return $return_var;
    }

    /**
     * Delete a record by ID
     *
     * @method delete
     * @param  integer          $id             The ID of the record
     *
     * @return boolean
     */
    public function delete($id)
    {
        if ($record = $this->find($id)) {
            if ($this->preDelete($id, $record)) {
                return $this->postDelete($id, $record->delete(), $record);
            }
        } else {
            $this->throwModelNotFoundException($id);
        }
    }

    /**
     * Pre delete, return false to prevent deletion
     *
     * @method preDelete
     * @param  [type]    $id     The ID of the record to be deleted
     * @param  Model     $record The model
     * @return boolean   Return true to continue with delete or false to abort
     */
    protected function preDelete($id, Model $record) : bool
    {
        return true;
    }

    /**
     * Post delete callback
     *
     * @method postDelete
     * @param  integer    $id         The ID of the deleted record
     * @param  boolean    $return_var The return var from the delete method on the model
     * @param  Model      $record     The model that was deleted
     *
     * @return boolean
     */
    protected function postDelete($id, $return_var, Model $record) : bool
    {
        return $return_var;
    }

    /**
     * Standard Paginator
     *
     * @method paginate
     * @param  integer          $perPage        The number of items per page
     *
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

    /**
     * Throw an exception when a model is not found
     *
     * @method throwModelNotFoundException
     * @param  integer $id
     */
    protected function throwModelNotFoundException($id)
    {
        throw new ModelNotFoundException('Model not found for ID ' . $id . ' on table ' . $this->container->make($this->getModelName())->getTable());
    }
}

<?php
namespace Croud\Repositories\Contracts;

interface RepositoryContract
{
    public function all($columns = ['*']);
    public function create(array $data);
    public function delete($id);
    public function find($id, $columns = ['*']);
    public function findBy($field, $value, $columns = ['*']);
    public function paginate($perPage = 20, $columns = ['*']);
    public function update(array $data, $id);
}

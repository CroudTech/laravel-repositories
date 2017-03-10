<?php
namespace Croud\Repositories\Contracts;

interface RepositoryContract
{
    public function all($columns = ['*']);
    public function create(array $data);
    public function delete($id);
    public function find($id);
    public function findBy($field, $value);
    public function paginate($perPage = 20);
    public function update($id, array $data);
}

<?php
namespace CroudTech\Repositories\TestApp\Controllers;

abstract class BaseController
{
    public $repository;

    public function __construct(\CroudTech\Repositories\Contracts\RepositoryContract $repository)
    {
        $this->repository = $repository;
    }
}

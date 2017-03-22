<?php
namespace CroudTech\Repositories;

use \CroudTech\Repositories\Exceptions\InvalidArgumentException;
use \CroudTech\Repositories\Exceptions\RepositoryExistsException;

class RepositoryGenerator
{
    /**
     * The fully qualified model name
     *
     * @method __construct
     * @param  string
     */
    protected $model_name;

    /**
     * The repository basename.
     *
     * @method __construct
     * @param  [type]      $model_name          [description]
     * @param  [type]      $repository_basename [description]
     */
    protected $repository_basename;

    /**
     *
     * @method __construct
     * @param  string      $model_name The fully qualified model name
     * @param  string      $repository_basename The basename of the repository to be created. Leave blank to auto-generate
     */
    public function __construct($model_name, $repository_basename = null)
    {
        $this->setModelName($model_name);
        $this->setRepositoryBaseName($repository_basename);
    }

    /**
     * Set the model name for the repository to be generated
     *
     * @method setModelName
     * @param  string      $model_name The fully qualified model name
     * @throws \InvalidArgumentException
     */
    public function setModelName($model_name)
    {
        if (!class_exists($model_name)) {
            throw new InvalidArgumentException('Class ' . $model_name . ' does not exist');
        }
        $this->model_name = $model_name;
    }

    /**
     * Set the repository name to be generated
     *
     * @method setRepositoryName
     * @param  string      $repository_name The fully qualified model name
     * @throws \InvalidArgumentException
     */
    public function setRepositoryBaseName($repository_basename)
    {
        $this->repository_basename = $repository_basename;
    }

    /**
     * Get the model name
     *
     * @method getModelName
     * @return string
     */
    public function getModelName()
    {
        return $this->model_name;
    }

    /**
     * Generate repository and contracts for model
     *
     * @method generateRepository
     * @return
     */
    public function generateRepository()
    {
        $contract_classname = $this->makeContract();
        $repository_classname = $this->makeRepository();
        return true;
    }

    /**
     * Make repository file
     *
     * @method makeRepository
     * @return string The fully qualified contract name
     */
    protected function makeRepository()
    {
        $full_path = $this->getFullRepositoryPath();
        if (file_exists($full_path)) {
            throw new RepositoryExistsException(sprintf('%s file already exists', $full_path));
        }
        $contract_contents = str_replace(
            [
                '%%MODEL_BASENAME%%',
                '%%MODEL_NAME%%',
                '%%CLASS_NAME%%',
                '%%NAMESPACE%%',
                '%%CONTRACT%%',
            ],
            [
                $this->getModelBasename(),
                $this->getModelName(),
                $this->getRepositoryBaseName(),
                trim($this->getRepositoriesNamespace(), '\\'),
                $this->getContractsNamespace() . '\\' . $this->getContractBaseName(),

            ],
            $this->contentsFromStub('Repository')
        );
        if (!file_exists(dirname($full_path))) {
            mkdir(dirname($full_path), 0755, true);
        }
        file_put_contents($full_path, $contract_contents);
    }

    /**
     * Make contract file
     *
     * @method makeContract
     * @return string The fully qualified contract name
     */
    protected function makeContract()
    {
        $full_path = $this->getFullContractPath();
        if (file_exists($full_path)) {
            return true;
        }
        $contract_contents = str_replace(
            [
                '%%MODEL_BASENAME%%',
                '%%MODEL_NAME%%',
                '%%CLASS_NAME%%',
                '%%NAMESPACE%%',
            ],
            [
                $this->getModelBasename(),
                $this->getModelName(),
                $this->getContractBaseName(),
                trim($this->getContractsNamespace(), '\\'),
            ],
            $this->contentsFromStub('Contract')
        );
        if (!file_exists(dirname($full_path))) {
            mkdir(dirname($full_path), 0755, true);
        }
        file_put_contents($full_path, $contract_contents);
    }

    /**
     * Get the model basename
     *
     * @method getModelBasename
     * @return {[type]          [description]
     */
    public function getModelBasename()
    {
        return class_basename($this->getModelName());
    }

    /**
     * Get the model namespace
     *
     * @method getModelNamespace
     * @return string
     */
    public function getModelNamespace()
    {
        return $this->getNamespaceFromClassname($this->getModelName());
    }

    /**
     * Get the namespace for the application.
     * Defaults to the namespace used by the model.
     *
     * @method getApplicationNamespace
     * @return string
     */
    public function getApplicationNamespace()
    {
        return config('repositories.app_namespace', $this->getNamespaceFromClassname($this->getModelNamespace()));
    }

    /**
     * Get the contract name
     *
     * @method getContractName
     * @return string
     */
    public function getRepositoryBasename()
    {
        return is_null($this->repository_basename) ? sprintf('%sRepository', $this->getModelBasename()) : $this->repository_basename;
    }

    /**
     * Get the repositories namespace for the application.
     *
     * @method getRepositoriesNamespace
     * @return string
     */
    public function getRepositoriesNamespace()
    {
        return config('repositories.repositories_namespace', $this->getApplicationNamespace() . '\Repositories');
    }

    /**
     * Get the path where contracts should be stored
     *
     * @method getContractsPath
     * @return string
     */
    public function getRepositoriesPath()
    {
        return config('repositories.repositories_path', app_path('Repositories'));
    }

    /**
     * Get the full path to the contract file
     *
     * @method getFullRepositoryPath
     * @return {[type]             [description]
     */
    public function getFullRepositoryPath()
    {
        return $this->getRepositoriesPath() . DIRECTORY_SEPARATOR . $this->getRepositoryBasename() . '.php';
    }

    /**
     * Get the contract name
     *
     * @method getContractName
     * @return string
     */
    public function getContractBasename()
    {
        return sprintf('%sRepositoryContract', $this->getModelBasename());
    }

    /**
     * Get the contracts namespace for the application.
     *
     * @method getRepositoriesNamespace
     * @return string
     */
    public function getContractsNamespace()
    {
        return config('repositories.contracts_namespace', $this->getApplicationNamespace() . '\Repositories\Contracts');
    }

    /**
     * Get the path where contracts should be stored
     *
     * @method getContractsPath
     * @return string
     */
    public function getContractsPath()
    {
        return config('repositories.contracts_path', app_path('Repositories/Contracts'));
    }

    /**
     * Get the full path to the contract file
     *
     * @method getFullContractPath
     * @return {[type]             [description]
     */
    public function getFullContractPath()
    {
        return $this->getContractsPath() . DIRECTORY_SEPARATOR . $this->getContractBasename() . '.php';
    }

    /**
     * [getNamespaceFromClassname description]
     * @method getNamespaceFromClassname
     * @param  [type]                    $class_name [description]
     * @return {[type]                               [description]
     */
    protected function getNamespaceFromClassname($class_name)
    {
        $parts = collect(explode('\\', trim($class_name, '\\')));
        $parts->pop();
        return '\\' . $parts->implode('\\');
    }



    /**
     * Get the contents from a stub file
     *
     * @method contentsFromStub
     * @return string
     */
    public function contentsFromStub($type)
    {
        return file_get_contents($this->getStubPath($type));
    }

    /**
     * The the path for the given stub type
     *
     * @method getStubPath
     * @param  string $type
     * @return string
     */
    public function getStubPath($type)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . $type . '.stub';
    }
}

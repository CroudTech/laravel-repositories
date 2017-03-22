<?php
namespace CroudTech\Repositories\Tests;

use \Illuminate\Database\Capsule\Manager as Capsule;
use \CroudTech\Repositories\TestApp\Models\User as UserModel;
use \CroudTech\Repositories\TestApp\Repositories\Contracts\UserRepositoryContract;
use \CroudTech\Repositories\TestApp\Repositories\UserRepository;
use \CroudTech\Repositories\TestApp\Repositories\UserApiRepository;
use \CroudTech\Repositories\TestApp\Controllers\UserController;
use \CroudTech\Repositories\TestApp\Controllers\UserApiController;
use \CroudTech\Repositories\Contracts\TransformerContract;
use \CroudTech\Repositories\Fractal;
use \Illuminate\Database\Eloquent\Builder as QueryBuilder;
use \Illuminate\Pagination\AbstractPaginator as Paginator;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use \Mockery as m;

class BaseRepositoryGeneratorTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $repository_generator = new \CroudTech\Repositories\RepositoryGenerator(\CroudTech\Repositories\TestApp\Models\TestModel::class);
        if (file_exists($repository_generator->getFullContractPath())) {
            unlink($repository_generator->getFullContractPath());
        }

        if (file_exists($repository_generator->getFullRepositoryPath())) {
            unlink($repository_generator->getFullRepositoryPath());
        }
    }

    /**
     * All tasks to reset the application state
     *
     * @method tearDown
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     *
     * @expectedException \CroudTech\Repositories\Exceptions\InvalidArgumentException
     *
     */
    public function testThrowsExceptionForMissingModel()
    {
        $this->loadUserData();
        $repository_generator = new \CroudTech\Repositories\RepositoryGenerator(\CroudTech\Repositories\TestApp\Models\MissingModel::class);
    }

    /**
     * @covers \CroudTech\Repositories\RepositoryGenerator::getModelBasename()
     *
     */
    public function testGetModelBasename()
    {
        $repository_generator = new \CroudTech\Repositories\RepositoryGenerator(\CroudTech\Repositories\TestApp\Models\TestModel::class);
        $this->assertEquals('TestModel', $repository_generator->getModelBasename());
    }

    /**
     * @covers \CroudTech\Repositories\RepositoryGenerator::getContractBasename()
     *
     */
    public function testGetContractBasename()
    {
        $repository_generator = new \CroudTech\Repositories\RepositoryGenerator(\CroudTech\Repositories\TestApp\Models\TestModel::class);
        $this->assertEquals('TestModelRepositoryContract', $repository_generator->getContractBasename());
    }

    /**
     * @covers \CroudTech\Repositories\RepositoryGenerator::getRepositoryBaseName()
     *
     */
    public function testGetRepositoryBasename()
    {
        $repository_generator = new \CroudTech\Repositories\RepositoryGenerator(\CroudTech\Repositories\TestApp\Models\TestModel::class);
        $this->assertEquals('TestModelRepository', $repository_generator->getRepositoryBaseName());
    }

    /**
     * @covers \CroudTech\Repositories\RepositoryGenerator::getRepositoryBaseName()
     *
     */
    public function testGetRepositoryBasenameWithCustomRepositoryName()
    {
        $repository_generator = new \CroudTech\Repositories\RepositoryGenerator(\CroudTech\Repositories\TestApp\Models\TestModel::class, 'CustomTestModelRepo');
        $this->assertEquals('CustomTestModelRepo', $repository_generator->getRepositoryBaseName());
    }

    /**
     * @covers \CroudTech\Repositories\RepositoryGenerator::getModelNamespace()
     * @covers \CroudTech\Repositories\RepositoryGenerator::getNamespaceFromClassname()
     *
     */
    public function testGetModelNamespace()
    {
        $repository_generator = new \CroudTech\Repositories\RepositoryGenerator(\CroudTech\Repositories\TestApp\Models\TestModel::class);
        $this->assertEquals('\CroudTech\Repositories\TestApp\Models', $repository_generator->getModelNamespace());
    }

    /**
     * @covers \CroudTech\Repositories\RepositoryGenerator::getApplicationNamespace()
     * @covers \CroudTech\Repositories\RepositoryGenerator::getNamespaceFromClassname()
     *
     */
    public function testGetApplicationNamespace()
    {
        $repository_generator = new \CroudTech\Repositories\RepositoryGenerator(\CroudTech\Repositories\TestApp\Models\TestModel::class);
        $this->assertEquals('\CroudTech\Repositories\TestApp', $repository_generator->getApplicationNamespace());
        config(['repositories.app_namespace' => '\Some\TestApp']);
        $this->assertEquals('\Some\TestApp', $repository_generator->getApplicationNamespace());
    }

    /**
     * @covers \CroudTech\Repositories\RepositoryGenerator::getContractsNamespace()
     * @covers \CroudTech\Repositories\RepositoryGenerator::getNamespaceFromClassname()
     */
    public function testGetContractsNamespace()
    {
        $repository_generator = new \CroudTech\Repositories\RepositoryGenerator(\CroudTech\Repositories\TestApp\Models\TestModel::class);
        $this->assertEquals('\CroudTech\Repositories\TestApp\Repositories\Contracts', $repository_generator->getContractsNamespace());
        config(['repositories.contracts_namespace' => '\Some\TestApp\Contracts']);
        $this->assertEquals('\Some\TestApp\Contracts', $repository_generator->getContractsNamespace());
        config(['repositories.contracts_namespace' => '\Some\TestApp\Repositories\Contracts']);
    }

    /**
     * @covers \CroudTech\Repositories\RepositoryGenerator::getRepositoriesNamespace()
     * @covers \CroudTech\Repositories\RepositoryGenerator::getNamespaceFromClassname()
     *
     */
    public function testGetRepositoriesNamespace()
    {
        $repository_generator = new \CroudTech\Repositories\RepositoryGenerator(\CroudTech\Repositories\TestApp\Models\TestModel::class);
        $this->assertEquals('\CroudTech\Repositories\TestApp\Repositories', $repository_generator->getRepositoriesNamespace());
        config(['repositories.repositories_namespace' => '\Some\TestApp\Repositories']);
        $this->assertEquals('\Some\TestApp\Repositories', $repository_generator->getRepositoriesNamespace());
    }

    public function testGetStubPath()
    {
        $repository_generator = new \CroudTech\Repositories\RepositoryGenerator(\CroudTech\Repositories\TestApp\Models\TestModel::class);
        $this->assertFileExists($repository_generator->getStubPath('Contract'));
        $this->assertFileExists($repository_generator->getStubPath('Repository'));
    }

    /**
     * @covers \CroudTech\Repositories\RepositoryGenerator::getContractsPath()
     *
     */
    public function testGetContractsPath()
    {
        $repository_generator = new \CroudTech\Repositories\RepositoryGenerator(\CroudTech\Repositories\TestApp\Models\TestModel::class);
        $this->assertEquals(app_path('Repositories/Contracts'), $repository_generator->getContractsPath());
        config(['repositories.contracts_path' => '/some/other/contracts/path']);
        $this->assertEquals('/some/other/contracts/path', $repository_generator->getContractsPath());
    }

    /**
     * @covers \CroudTech\Repositories\RepositoryGenerator::getFullContractPath()
     *
     */
    public function testGetFullContractPath()
    {
        $repository_generator = new \CroudTech\Repositories\RepositoryGenerator(\CroudTech\Repositories\TestApp\Models\TestModel::class);
        $this->assertEquals(app_path('Repositories/Contracts') . '/TestModelRepositoryContract.php', $repository_generator->getFullContractPath());
    }

    /**
     * @covers \CroudTech\Repositories\RepositoryGenerator::getFullRepositoryPath()
     *
     */
    public function testGetFullRepositoryPath()
    {
        $repository_generator = new \CroudTech\Repositories\RepositoryGenerator(\CroudTech\Repositories\TestApp\Models\TestModel::class);
        $this->assertEquals(app_path('Repositories') . '/TestModelRepository.php', $repository_generator->getFullRepositoryPath());
    }

    /**
     * @covers \CroudTech\Repositories\RepositoryGenerator::generateRepository()
     */
    public function testCreateRepositoryGenerateFile()
    {
        $repository_generator = new \CroudTech\Repositories\RepositoryGenerator(\CroudTech\Repositories\TestApp\Models\TestModel::class);
        $this->assertTrue($repository_generator->generateRepository());
        $this->assertFileExists($repository_generator->getFullContractPath());
        $this->assertFileExists($repository_generator->getFullRepositoryPath());
    }

    /**
     * @covers \CroudTech\Repositories\RepositoryGenerator::generateRepository()
     */
    public function testCreateRepositoryGenerateFileThrowsExceptionWhenRepositoryClassesExist()
    {
        $repository_generator = new \CroudTech\Repositories\RepositoryGenerator(\CroudTech\Repositories\TestApp\Models\TestModel::class);
        $this->assertTrue($repository_generator->generateRepository());
        $this->assertFileExists($repository_generator->getFullContractPath());
        $this->expectException(\CroudTech\Repositories\Exceptions\RepositoryExistsException::class);
        $repository_generator->generateRepository();
    }
}

<?php
namespace Croud\RepositoryTests;

use \Illuminate\Database\Capsule\Manager as Capsule;
use \Croud\RepositoryTests\Models\User as UserModel;
use \Croud\RepositoryTests\Repositories\Contracts\UserRepositoryContract;
use \Croud\RepositoryTests\Repositories\UserRepository;

class BaseRepositoryTest extends TestCase
{
    /**
     * Load the user data for tests
     *
     * @method loadUserData
     */
    protected function loadUserData()
    {
        $data = include __DIR__.'/data/users.php';
        Capsule::table('users')->insert($data);
    }

    /**
     * Test provider returns correct instance type
     *
     * @method testProvider
     */
    public function testProvider()
    {
        $repository = $this->app->make(UserRepositoryContract::class);
        $this->assertInstanceOf(UserRepositoryContract::class, $repository);
        $this->assertInstanceOf(UserRepository::class, $repository);
    }

    /**
     * @covers \Croud\Repositories\BaseRepository::all()
     */
    public function testAllMethodReturnsCollection()
    {
        $this->loadUserData();
        $repository = $this->app->make(UserRepositoryContract::class);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $this->app->make(UserRepositoryContract::class)->all());
    }

    /**
     * @covers \Croud\Repositories\BaseRepository::all()
     */
    public function testAllMethodReturnsCorrectNumberOfRecords()
    {
        $this->loadUserData();
        $this->assertEquals(10, $this->app->make(UserRepositoryContract::class)->all()->count());
    }
}

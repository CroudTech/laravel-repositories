<?php
namespace CroudTech\RepositoryTests;

use \Illuminate\Database\Capsule\Manager as Capsule;
use \CroudTech\RepositoryTests\Models\User as UserModel;
use \CroudTech\RepositoryTests\Repositories\Contracts\UserRepositoryContract;
use \CroudTech\RepositoryTests\Repositories\UserRepository;
use \CroudTech\Repositories\Contracts\TransformerContract;
use \Illuminate\Database\Eloquent\Builder as QueryBuilder;
use \Illuminate\Pagination\AbstractPaginator as Paginator;
use \Illuminate\Database\Eloquent\ModelNotFoundException;

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
     * @covers \CroudTech\Repositories\BaseRepository::query()
     */
    public function testQueryReturnsQueryBuilder()
    {
        $this->assertInstanceOf(QueryBuilder::class, $this->app->make(UserRepositoryContract::class)->query());
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::all()
     */
    public function testAllMethodReturnsCollection()
    {
        $this->loadUserData();
        $repository = $this->app->make(UserRepositoryContract::class);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $this->app->make(UserRepositoryContract::class)->all());
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::all()
     */
    public function testAllMethodReturnsCorrectNumberOfRecords()
    {
        $this->loadUserData();
        $this->assertEquals(10, $this->app->make(UserRepositoryContract::class)->all()->count());
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::all()
     */
    public function testAllMethodReturnsCorrectNumberOfRecordsWithModifiedQuery()
    {
        $this->loadUserData();
        $repository = $this->app->make(UserRepositoryContract::class);
        $this->assertEquals(10, $repository->all()->count());

        $repository->query()->where('name', 'Test User 1');
        $this->assertEquals(1, $repository->all()->count());
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::clearQuery()
     */
    public function testClearQueryReturnsQueryBuilderInstance()
    {
        $repository = $this->app->make(UserRepositoryContract::class);
        $this->assertInstanceOf(QueryBuilder::class, $repository->clearQuery());
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::clearQuery()
     */
    public function testClearQueryClearsAllConstraints()
    {
        $this->loadUserData();
        $repository = $this->app->make(UserRepositoryContract::class);
        $this->assertEquals(10, $repository->all()->count());
        $repository->query()->where('name', 'Test User 1');
        $this->assertEquals(1, $repository->all()->count());

        $repository->clearQuery();
        $this->assertEquals(10, $repository->all()->count());
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::create()
     */
    public function testCreateReturnsCorrectObjectInstance()
    {
        $repository = $this->app->make(UserRepositoryContract::class);
        $this->assertInstanceOf(UserModel::class, $repository->create(['name' => 'Test Name']));
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::create()
     */
    public function testCreateReturnsObjectWithCorrectData()
    {
        $repository = $this->app->make(UserRepositoryContract::class);
        $this->assertEquals('Test Name', $repository->create(['name' => 'Test Name'])->name);
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::create()
     */
    public function testCreateObjectExistsInTheDatabase()
    {
        $repository = $this->app->make(UserRepositoryContract::class);
        $repository->create(['name' => 'Test Name 123']);
        $this->assertInstanceOf(UserModel::class, UserModel::where('name', 'Test Name 123')->first());
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::find()
     */
    public function testFind()
    {
        $repository = $this->app->make(UserRepositoryContract::class);
        $record = $repository->create(['name' => 'Test Name 123']);
        $this->assertInstanceOf(UserModel::class, $found_record = $repository->find($record->id));
        $this->assertEquals($record->name, $found_record->name);
    }

    /**
     * @covers CroudTech\Repositories\BaseRepository::delete()
     */
    public function testDelete()
    {
        $repository = $this->app->make(UserRepositoryContract::class);
        $record = $repository->create(['name' => 'Test Name 123']);
        $this->assertTrue($repository->delete($record->id));
        $this->assertNull($repository->find($record->id));
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::findBy()
     */
    public function testFindBy()
    {
        $repository = $this->app->make(UserRepositoryContract::class);
        $record = $repository->create(['name' => 'Test Name 123']);
        $this->assertInstanceOf(UserModel::class, $found_record = $repository->findBy('name', $record->name));
        $this->assertEquals($record->name, $found_record->name);
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::update()
     */
    public function testUpdate()
    {
        $repository = $this->app->make(UserRepositoryContract::class);
        $record = $repository->create(['name' => 'Test Name 123']);
        $this->assertTrue($repository->update($record->id, ['name' => 'Updated Name']));
        $updated_record = $repository->find($record->id);
        $this->assertEquals('Updated Name', $updated_record->name);
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::update()
     */
    public function testUpdateThrowsExceptionWhenNoRecordIsFound()
    {
        $this->expectException(ModelNotFoundException::class);
        $repository = $this->app->make(UserRepositoryContract::class);
        $this->assertTrue($repository->update(94321, ['name' => 'Updated Name']));
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::paginate()
     */
    public function testPaginateReturnsCorrectInstanceType()
    {
        $this->loadUserData();
        $repository = $this->app->make(UserRepositoryContract::class);
        $this->assertInstanceOf(Paginator::class, $repository->paginate(5));
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::paginate()
     */
    public function testPaginateReturnsCorrectNumberOfItems()
    {
        $this->loadUserData();
        $repository = $this->app->make(UserRepositoryContract::class);
        $this->assertEquals(5, $repository->paginate(5)->count());
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::paginate()
     */
    public function testPaginateUsesRepositoryQueryConstraints()
    {
        $this->loadUserData();
        $repository = $this->app->make(UserRepositoryContract::class);
        $repository->query()->where('name', 'Test User 1');
        $this->assertEquals(1, $repository->paginate(10)->count());
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::simplePaginate()
     */
    public function testSimplePaginateReturnsCorrectInstanceType()
    {
        $this->loadUserData();
        $repository = $this->app->make(UserRepositoryContract::class);
        $this->assertInstanceOf(Paginator::class, $repository->simplePaginate(5));
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::simplePaginate()
     */
    public function testSimplePaginateReturnsCorrectNumberOfItems()
    {
        $this->loadUserData();
        $repository = $this->app->make(UserRepositoryContract::class);
        $this->assertEquals(5, $repository->simplePaginate(5)->count());
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::simplePaginate()
     */
    public function testSimplePaginateUsesRepositoryQueryConstraints()
    {
        $this->loadUserData();
        $repository = $this->app->make(UserRepositoryContract::class);
        $repository->query()->where('name', 'Test User 1');
        $this->assertEquals(1, $repository->simplePaginate(10)->count());
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::__construct()
     * @covers \CroudTech\Repositories\BaseRepository::getTransformer()
     * @covers \CroudTech\Repositories\BaseRepository::setTransformer()
     */
    public function testTransformerInjection() {
        $repository = $this->app->make(UserRepositoryContract::class);
        $this->assertInstanceOf(TransformerContract::class, $repository->getTransformer());
    }
}

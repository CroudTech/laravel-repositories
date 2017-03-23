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

class BaseRepositoryTest extends TestCase
{
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
     * Test provider returns correct instance type
     *
     * @method testProvider
     */
    public function testDiInjectsCorrectRepository()
    {
        $repository = $this->app->make(UserRepositoryContract::class);
        $this->assertInstanceOf(UserRepositoryContract::class, $repository);
        $this->assertInstanceOf(UserRepository::class, $repository);
    }

    /**
     * Test provider returns correct instance type
     *
     * @method testProvider
     */
    public function testContextualDiInjectsCorrectRepository()
    {
        $user_controller = $this->app->make(UserController::class);
        $this->assertInstanceOf(UserRepositoryContract::class, $user_controller->repository);
        $this->assertInstanceOf(UserRepository::class, $user_controller->repository);

        $user_api_controller = $this->app->make(UserApiController::class);
        $this->assertInstanceOf(UserRepositoryContract::class, $user_api_controller->repository);
        $this->assertInstanceOf(UserApiRepository::class, $user_api_controller->repository);
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
        $this->assertInstanceOf(UserModel::class, $repository->create(['name' => 'Test Name', 'first_name' => 'First Name', 'last_name' => 'Last Name']));
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::create()
     */
    public function testCreateReturnsObjectWithCorrectData()
    {
        $repository = $this->app->make(UserRepositoryContract::class);
        $this->assertEquals('Test Name', $repository->create(['name' => 'Test Name', 'first_name' => 'First Name', 'last_name' => 'Last Name'])->name);
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::create()
     */
    public function testCreateObjectExistsInTheDatabase()
    {
        $repository = $this->app->make(UserRepositoryContract::class);
        $repository->create(['name' => 'Test Name 123', 'first_name' => 'First Name', 'last_name' => 'Last Name']);
        $this->assertInstanceOf(UserModel::class, UserModel::where('name', 'Test Name 123')->first());
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::find()
     */
    public function testFind()
    {
        $repository = $this->app->make(UserRepositoryContract::class);
        $record = $repository->create(['name' => 'Test Name 123', 'first_name' => 'First Name', 'last_name' => 'Last Name']);
        $this->assertInstanceOf(UserModel::class, $found_record = $repository->find($record->id));
        $this->assertEquals($record->name, $found_record->name);
    }

    /**
     * @covers CroudTech\Repositories\BaseRepository::delete()
     */
    public function testDelete()
    {
        $repository = $this->app->make(UserRepositoryContract::class);
        $record = $repository->create(['name' => 'Test Name 123', 'first_name' => 'First Name', 'last_name' => 'Last Name']);
        $this->assertTrue($repository->delete($record->id));
        $this->assertNull($repository->find($record->id));
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::findBy()
     */
    public function testFindBy()
    {
        $repository = $this->app->make(UserRepositoryContract::class);
        $record = $repository->create(['name' => 'Test Name 123', 'first_name' => 'First Name', 'last_name' => 'Last Name']);
        $this->assertInstanceOf(UserModel::class, $found_record = $repository->findBy('name', $record->name));
        $this->assertEquals($record->name, $found_record->name);
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::update()
     */
    public function testUpdate()
    {
        $repository = $this->app->make(UserRepositoryContract::class);
        $record = $repository->create(['name' => 'Test Name 123', 'first_name' => 'First Name', 'last_name' => 'Last Name']);
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
    public function testTransformerInjection()
    {
        $repository = $this->app->make(UserRepositoryContract::class);
        $this->assertInstanceOf(TransformerContract::class, $repository->getTransformer());
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::item()
     *
     */
    public function testFractalItem()
    {
        $this->loadUserData();
        $repository = $this->app->make(UserRepositoryContract::class);
        $request = m::mock(\Illuminate\Http\Request::class);
        $request->shouldReceive('offsetExists');
        $request->shouldReceive('route');
        $fractal = new Fractal($request);
        $item = $fractal->item($repository->query()->first(), $repository->getTransformer(), $repository->getModelName());
        $this->assertInternalType('array', $item);
        $this->assertArrayHasKey('data', $item);
        $this->assertArrayHasKey('first_name', $item['data']);
        $this->assertArrayHasKey('last_name', $item['data']);
    }

    /**
     * @covers \CroudTech\Repositories\Fractal::collection()
     *
     */
    public function testFractalCollection()
    {
        $this->loadUserData();
        $repository = $this->app->make(UserRepositoryContract::class);
        $request = m::mock(\Illuminate\Http\Request::class);
        $request->shouldReceive('offsetExists');
        $request->shouldReceive('route');
        $fractal = new Fractal($request);
        $items = $fractal->collection($repository->all(), $repository->getTransformer(), $repository->getModelName());


        $this->assertInternalType('array', $items);
        $this->assertArrayHasKey('data', $items);
        $this->assertArrayHasKey('first_name', last($items['data']));
        $this->assertArrayHasKey('last_name', last($items['data']));
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::transformItem()
     *
     * @method testTransformItem
     * @return Test that an item can be transformed
     */
    public function testTransformItem()
    {
        $this->loadUserData();
        $repository = $this->app->make(UserRepositoryContract::class);
        $item = $repository->all()->first();
        $transformed = $repository->transformItem($item);

        $this->assertArrayHasKey('data', $transformed);
        $this->assertArrayHasKey('first_name', $transformed['data']);
        $this->assertArrayHasKey('last_name', $transformed['data']);
        $this->assertFalse(isset($transformed['data']['name']));
        $this->assertFalse(isset($transformed['data']['address']));
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::transformItem()
     *
     * @method testTransformItem
     * @return Test that an item can be transformed
     */
    public function testTransformItemWithIncludes()
    {
        $this->loadUserData();
        $repository = $this->app->make(UserRepositoryContract::class);
        $item = $repository->all()->first();
        $transformed = $repository->transformItem($item, ['address']);

        $this->assertArrayHasKey('data', $transformed);
        $this->assertArrayHasKey('first_name', $transformed['data']);
        $this->assertArrayHasKey('last_name', $transformed['data']);
        $this->assertArrayHasKey('address', $transformed['data']);
        $this->assertArrayHasKey('data', $transformed['data']['address']);

        // Check that the includes are cleared
        $transformed = $repository->transformItem($item);
        $this->assertFalse(isset($transformed['data']['address']));
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::transformCollection()
     *
     * @method testTransformItem
     * @return Test that an item can be transformed
     */
    public function testTransformCollection()
    {
        $this->loadUserData();
        $repository = $this->app->make(UserRepositoryContract::class);
        $items = $repository->all();
        $transformed = $repository->transformCollection($items);

        $this->assertArrayHasKey('data', $transformed);
        $this->assertArrayHasKey('first_name', last($transformed['data']));
        $this->assertArrayHasKey('last_name', last($transformed['data']));
    }

    /**
     * @covers \CroudTech\Repositories\BaseRepository::transformCollection()
     *
     * @method testTransformItem
     * @return Test that an item can be transformed
     */
    public function testTransformCollectionWithIncludes()
    {
        $this->loadUserData();
        $repository = $this->app->make(UserRepositoryContract::class);
        $items = $repository->all();
        $transformed = $repository->transformCollection($items, ['address']);


        $this->assertArrayHasKey('data', $transformed);
        $this->assertArrayHasKey('first_name', last($transformed['data']));
        $this->assertArrayHasKey('last_name', last($transformed['data']));
        $this->assertArrayHasKey('address', last($transformed['data']));
        $this->assertArrayHasKey('data', last($transformed['data'])['address']);

        $transformed = $repository->transformCollection($items);
        $this->assertFalse(isset(last($transformed['data'])['address']));
    }
}

<?php
namespace Croud\RepositoryTests;

use Illuminate\Database\Capsule\Manager as Capsule;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    /**
     * Create the application used for our tests
     *
     * @method createApplication
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication() : \Illuminate\Foundation\Application
    {
        $app = require __DIR__.'/bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }

    /**
     * [setUp description]
     * @method setUp
     */
    public function setUp()
    {
        parent::setUp();
        $this->migrate();
    }

    /**
     * Create the test database schema
     *
     * @method migrate
     *
     */
    protected function migrate()
    {
        $schema = Capsule::schema();
        $schema->dropIfExists('users');
        $schema->create('users', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->softDeletes();
        });
    }
}

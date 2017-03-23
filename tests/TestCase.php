<?php
namespace CroudTech\Repositories\Tests;

use Illuminate\Database\Capsule\Manager as Capsule;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
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

        foreach (\CroudTech\Repositories\TestApp\Models\User::all() as $user) {
            $address = \CroudTech\Repositories\TestApp\Models\Address::create([
                'address_line_1' => sprintf('Address for user %s', $user->id),
            ]);
            $user->address_id = $address->id;
            $user->save();
            for ($email_number = 1; $email_number <= 10; $email_number++) {
                $email = \CroudTech\Repositories\TestApp\Models\Email::create(
                    [
                        'email' => sprintf('email%s_%s', $email_number, $user->id),
                        'user_id' => $user->id,
                    ]
                );
            }
        }
    }

    /**
     * Create the application used for our tests
     *
     * @method createApplication
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication() : \Illuminate\Foundation\Application
    {
        $app = require __DIR__.'/testapp/bootstrap/app.php';
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
            $table->string('first_name');
            $table->string('last_name');
            $table->unsignedInteger('address_id')->nullable();
            $table->softDeletes();
        });

        $schema->dropIfExists('addresses');
        $schema->create('addresses', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->string('address_line_1');
            $table->softDeletes();
        });

        $schema->dropIfExists('emails');
        $schema->create('emails', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->unsignedInteger('user_id');
            $table->softDeletes();
        });
    }
}

<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use App\Jobs\RemoveUserCacheJob;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use Mockery;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_index_returns_paginated_users()
    {
        User::factory()->count(15)->create();
        $controller = new UserController();
        $request = Request::create('/users', 'GET');
        $response = $controller->index($request);
        $reflection = new \ReflectionClass($response);
        $propsProperty = $reflection->getProperty('props');
        $propsProperty->setAccessible(true);
        $props = $propsProperty->getValue($response);
        $users = $props['users'];
        $this->assertTrue($users->total() >= 12);
    }

    public function test_store_creates_user_and_address()
    {
        $controller = new UserController();
        $request = Request::create('/users', 'POST', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'address' => [
                'country' => 'Country',
                'city' => 'City',
                'post_code' => '12345',
                'street' => 'Main St',
            ],
        ]);
        $mockRequest = Mockery::mock('App\Http\Requests\UserRequest');
        $mockRequest->shouldReceive('validated')->andReturn($request->all());
        $response = $controller->store($mockRequest);
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
        $this->assertDatabaseHas('addresses', ['city' => 'City']);
    }

    public function test_show_returns_user_from_cache_or_db()
    {
        $user = User::factory()->create();
        $controller = new UserController();
        $key = "user:{$user->id}";
        Cache::put($key, $user->toArray(), now()->addMinutes(5));
        $response = $controller->show($user->id);
        $data = $response->getData();
        $this->assertEquals($user->id, $data->id);
    }

    public function test_update_modifies_user_and_address()
    {
        $user = User::factory()->create();
        $controller = new UserController();
        $request = Request::create('/users/'.$user->id, 'PUT', [
            'first_name' => 'Jane',
            'address' => [
                'country' => 'NewCountry',
                'city' => 'NewCity',
                'post_code' => '54321',
                'street' => 'Second St',
            ],
        ]);
        $mockRequest = Mockery::mock('App\Http\Requests\UserRequest');
        $mockRequest->shouldReceive('validated')->andReturn($request->all());
        $controller->update($mockRequest, $user);
        $this->assertDatabaseHas('users', ['first_name' => 'Jane']);
    }

    public function test_destroy_deletes_user_and_dispatches_job()
    {
        Bus::fake();
        $user = User::factory()->create();
        $controller = new UserController();
        $controller->destroy($user);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        Bus::assertDispatched(RemoveUserCacheJob::class, function ($job) use ($user) {
            return $job->userId === $user->id;
        });
    }
}

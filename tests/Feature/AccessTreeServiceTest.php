<?php

namespace Obrainwave\AccessTree\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Obrainwave\AccessTree\Tests\TestCase;
use Obrainwave\AccessTree\Models\Permission;
use Obrainwave\AccessTree\Models\Role;
use App\Models\User;
use Obrainwave\AccessTree\Contracts\AccessTreeServiceInterface;

class AccessTreeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AccessTreeServiceInterface $accessTreeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accessTreeService = app(AccessTreeServiceInterface::class);
    }

    /** @test */
    public function it_can_create_permission()
    {
        $data = [
            'name' => 'Test Permission',
            'status' => 1
        ];

        $response = $this->accessTreeService->createPermission($data);

        $this->assertTrue($response->isSuccess());
        $this->assertDatabaseHas('permissions', ['name' => 'Test Permission']);
    }

    /** @test */
    public function it_can_create_role_with_permissions()
    {
        $permission = Permission::create([
            'name' => 'Test Permission',
            'slug' => 'test_permission',
            'status' => 1
        ]);

        $data = [
            'name' => 'Test Role',
            'status' => 1
        ];

        $response = $this->accessTreeService->createRole($data, [$permission->id]);

        $this->assertTrue($response->isSuccess());
        $this->assertDatabaseHas('roles', ['name' => 'Test Role']);
    }

    /** @test */
    public function it_can_assign_role_to_user()
    {
        $user = User::factory()->create();
        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test_role',
            'status' => 1
        ]);

        $response = $this->accessTreeService->assignRoleToUser($user->id, $role->id);

        $this->assertTrue($response->isSuccess());
        $this->assertTrue($user->fresh()->hasRole('test_role'));
    }

    /** @test */
    public function it_can_check_user_permissions()
    {
        $user = User::factory()->create();
        $permission = Permission::create([
            'name' => 'Test Permission',
            'slug' => 'test_permission',
            'status' => 1
        ]);
        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test_role',
            'status' => 1
        ]);
        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $this->assertTrue($this->accessTreeService->checkPermission('test_permission', $user->id));
    }

    /** @test */
    public function it_can_check_user_roles()
    {
        $user = User::factory()->create();
        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test_role',
            'status' => 1
        ]);
        $user->roles()->attach($role->id);

        $this->assertTrue($this->accessTreeService->checkRole('test_role', $user->id));
    }

    /** @test */
    public function root_user_bypasses_all_checks()
    {
        $user = User::factory()->create(['is_root_user' => true]);

        $this->assertTrue($this->accessTreeService->checkPermission('any_permission', $user->id));
        $this->assertTrue($this->accessTreeService->checkRole('any_role', $user->id));
    }

    /** @test */
    public function it_validates_permission_data()
    {
        $data = [
            'name' => '', // Invalid: empty name
            'status' => 1
        ];

        $response = $this->accessTreeService->createPermission($data);

        $this->assertTrue($response->isError());
        $this->assertArrayHasKey('name', $response->errors);
    }

    /** @test */
    public function it_prevents_duplicate_permission_names()
    {
        Permission::create([
            'name' => 'Existing Permission',
            'slug' => 'existing_permission',
            'status' => 1
        ]);

        $data = [
            'name' => 'Existing Permission',
            'status' => 1
        ];

        $response = $this->accessTreeService->createPermission($data);

        $this->assertTrue($response->isError());
    }
}

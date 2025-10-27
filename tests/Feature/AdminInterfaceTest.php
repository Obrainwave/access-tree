<?php

namespace Obrainwave\AccessTree\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Obrainwave\AccessTree\Tests\TestCase;
use App\Models\User;
use Obrainwave\AccessTree\Models\Permission;
use Obrainwave\AccessTree\Models\Role;

class AdminInterfaceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_dashboard_is_accessible()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/admin/accesstree');

        $response->assertStatus(200);
        $response->assertSee('AccessTree');
    }

    /** @test */
    public function permissions_index_is_accessible()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/admin/accesstree/permissions');

        $response->assertStatus(200);
        $response->assertSee('Permissions');
    }

    /** @test */
    public function can_create_permission_through_admin()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $permissionData = [
            'name' => 'Test Permission',
            'status' => 1
        ];

        $response = $this->post('/admin/accesstree/permissions', $permissionData);

        $response->assertRedirect();
        $this->assertDatabaseHas('permissions', ['name' => 'Test Permission']);
    }

    /** @test */
    public function can_create_role_through_admin()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $permission = Permission::create([
            'name' => 'Test Permission',
            'slug' => 'test_permission',
            'status' => 1
        ]);

        $roleData = [
            'name' => 'Test Role',
            'status' => 1,
            'permissions' => [$permission->id]
        ];

        $response = $this->post('/admin/accesstree/roles', $roleData);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', ['name' => 'Test Role']);
    }

    /** @test */
    public function can_search_permissions()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Permission::create([
            'name' => 'Create Users',
            'slug' => 'create_users',
            'status' => 1
        ]);

        Permission::create([
            'name' => 'Delete Posts',
            'slug' => 'delete_posts',
            'status' => 1
        ]);

        $response = $this->get('/admin/accesstree/permissions?search=users');

        $response->assertStatus(200);
        $response->assertSee('Create Users');
        $response->assertDontSee('Delete Posts');
    }

    /** @test */
    public function can_assign_roles_to_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $role = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'status' => 1
        ]);

        $targetUser = User::factory()->create();

        $response = $this->post("/admin/accesstree/users/{$targetUser->id}/roles", [
            'roles' => [$role->id]
        ]);

        $response->assertRedirect();
        $this->assertTrue($targetUser->fresh()->hasRole('admin'));
    }

    /** @test */
    public function can_toggle_root_user_status()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $targetUser = User::factory()->create(['is_root_user' => false]);

        $response = $this->post("/admin/accesstree/users/{$targetUser->id}/toggle-root");

        $response->assertRedirect();
        $this->assertTrue($targetUser->fresh()->is_root_user);
    }
}

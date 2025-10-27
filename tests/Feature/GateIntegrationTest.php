<?php

namespace Obrainwave\AccessTree\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Obrainwave\AccessTree\Tests\TestCase;
use App\Models\User;
use Obrainwave\AccessTree\Models\Permission;
use Obrainwave\AccessTree\Models\Role;
use Illuminate\Support\Facades\Gate;

class GateIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function gates_are_registered_automatically()
    {
        $this->assertTrue(Gate::has('manage-users'));
        $this->assertTrue(Gate::has('view-users'));
        $this->assertTrue(Gate::has('create-users'));
        $this->assertTrue(Gate::has('edit-users'));
        $this->assertTrue(Gate::has('delete-users'));
    }

    /** @test */
    public function user_with_permission_can_access_gate()
    {
        $user = User::factory()->create();
        $permission = Permission::create([
            'name' => 'Manage Users',
            'slug' => 'manage_users',
            'status' => 1
        ]);
        $role = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'status' => 1
        ]);
        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $this->actingAs($user);

        $this->assertTrue(Gate::allows('manage-users'));
    }

    /** @test */
    public function user_without_permission_cannot_access_gate()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertFalse(Gate::allows('manage-users'));
    }

    /** @test */
    public function root_user_can_access_all_gates()
    {
        $user = User::factory()->create(['is_root_user' => true]);
        $this->actingAs($user);

        $this->assertTrue(Gate::allows('manage-users'));
        $this->assertTrue(Gate::allows('manage-roles'));
        $this->assertTrue(Gate::allows('manage-permissions'));
    }

    /** @test */
    public function can_use_gates_in_controllers()
    {
        $user = User::factory()->create();
        $permission = Permission::create([
            'name' => 'View Users',
            'slug' => 'view_users',
            'status' => 1
        ]);
        $role = Role::create([
            'name' => 'Viewer',
            'slug' => 'viewer',
            'status' => 1
        ]);
        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $this->actingAs($user);

        $response = $this->get('/admin/accesstree/users');

        $response->assertStatus(200);
    }

    /** @test */
    public function unauthorized_user_cannot_access_protected_route()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/admin/accesstree/users');

        $response->assertStatus(403);
    }
}

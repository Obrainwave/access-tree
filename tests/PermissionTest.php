<?php

namespace Obrainwave\AccessTree\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Obrainwave\AccessTree\Tests\TestCase;
use Obrainwave\AccessTree\Models\Permission;

class PermissionTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  function createPermission()
  {
    $permission = Permission::factory()->create(['name' => 'Add User', 'slug' => 'add_user']);
    $this->assertEquals('Add User', $permission->name);
    $this->assertEquals('add_user', $permission->slug);
    echo 'Permission added successfully';
    greet('Ola');
    $this->assertCount(1, Permission::all());
  }
}

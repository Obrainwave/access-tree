<?php

namespace Obrainwave\AccessTree\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Obrainwave\AccessTree\Tests\TestCase;
use Obrainwave\AccessTree\Models\RoleHasPermission;

class RoleHasPermissionTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  function createRoleHasPermission()
  {
    $role = RoleHasPermission::factory()->create(['name' => 'Admin', 'slug' => 'admin']);
    $this->assertEquals('Admin', $role->name);
    $this->assertEquals('admin', $role->slug);
    echo 'Role added successfully';
  }
}

<?php

namespace Obrainwave\AccessTree\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Obrainwave\AccessTree\Tests\TestCase;
use Obrainwave\AccessTree\Models\Role;

class RoleTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  function createRole()
  {
    $role = Role::factory()->create(['name' => 'Admin', 'slug' => 'admin']);
    $this->assertEquals('Admin', $role->name);
    $this->assertEquals('admin', $role->slug);
    echo 'Role added successfully';
  }
}

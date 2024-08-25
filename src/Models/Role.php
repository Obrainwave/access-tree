<?php

namespace Obrainwave\AccessTree\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use \Obrainwave\AccessTree\Models\RoleHasPermission;

class Role extends Model
{
  use HasFactory;

  // Disable Laravel's mass assignment protection
  protected $guarded = [];

  public function rolePermissions()
  {
    return $this->hasMany(RoleHasPermission::class, 'role_id');
  }
}

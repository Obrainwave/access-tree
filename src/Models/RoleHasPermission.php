<?php

namespace Obrainwave\AccessTree\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Obrainwave\AccessTree\Models\Role;
use Obrainwave\AccessTree\Models\Permission;

class RoleHasPermission extends Model
{
  use HasFactory;

  // Disable Laravel's mass assignment protection
  protected $guarded = [];

  public function permission()
  {
    return $this->belongsTo(Permission::class, 'permission_id');
  }

  public function role()
  {
    return $this->belongsTo(Role::class, 'role_id');
  }
}

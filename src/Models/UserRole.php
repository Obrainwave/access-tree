<?php

namespace Obrainwave\AccessTree\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use Obrainwave\AccessTree\Models\Role;
use Obrainwave\AccessTree\Models\Permission;

class UserRole extends Model
{
  use HasFactory;

  // Disable Laravel's mass assignment protection
  protected $guarded = [];

  public function user()
  {
    $userModel = config('accesstree.user_model', 'App\\Models\\User');
    return $this->belongsTo($userModel, 'user_id');
  }

  public function role()
  {
    return $this->belongsTo(Role::class, 'role_id');
  }
}

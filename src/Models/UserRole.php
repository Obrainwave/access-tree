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
    return $this->belongsTo(User::class, 'user_id');
  }

  public function role()
  {
    return $this->belongsTo(Role::class, 'role_id');
  }
}

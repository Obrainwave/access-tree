<?php
namespace Obrainwave\AccessTree\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions')->withTimestamps();
    }

    public function users()
    {
        $userModel = config('accesstree.user_model', 'App\\Models\\User');
        return $this->belongsToMany($userModel, 'user_roles')->withTimestamps();
    }
}

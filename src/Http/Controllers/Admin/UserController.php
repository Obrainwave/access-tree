<?php

namespace Obrainwave\AccessTree\Http\Controllers\Admin;

use Obrainwave\AccessTree\Models\Role;
use Obrainwave\AccessTree\Contracts\AccessTreeServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class UserController extends ResourceController
{
    protected $resourceName = 'users';
    
    public function __construct(
        private AccessTreeServiceInterface $accessTreeService
    ) {
        $this->model = $this->getUserModel();
        parent::__construct();
    }
    
    protected function getUserModel()
    {
        // Try to get the user model from config
        $userModel = config('accesstree.user_model', 'App\\Models\\User');
        
        // If the configured model doesn't exist, try common alternatives
        if (!class_exists($userModel)) {
            $alternatives = [
                'App\\User',
                'App\\Models\\User',
                'Illuminate\\Foundation\\Auth\\User'
            ];
            
            foreach ($alternatives as $alternative) {
                if (class_exists($alternative)) {
                    return $alternative;
                }
            }
            
            // If no user model is found, throw an exception with helpful message
            throw new \Exception(
                'User model not found. Please configure the user model in config/accesstree.php ' .
                'or ensure you have a User model in your application.'
            );
        }
        
        return $userModel;
    }
    
    protected function getSearchableFields(): array
    {
        return ['name', 'email'];
    }
    
    protected function getTableColumns(): array
    {
        return [
            'name' => 'Name',
            'email' => 'Email',
            'roles_count' => 'Roles',
            'is_root_user' => 'Root User',
            'created_at' => 'Created',
            'actions' => 'Actions'
        ];
    }
    
    protected function getFormFields(): array
    {
        return [
            'name' => [
                'type' => 'text',
                'label' => 'Name',
                'required' => true,
                'placeholder' => 'Enter user name'
            ],
            'email' => [
                'type' => 'email',
                'label' => 'Email',
                'required' => true,
                'placeholder' => 'Enter user email'
            ],
            'roles' => [
                'type' => 'checkbox-group',
                'label' => 'Roles',
                'options' => Role::where('status', 1)->pluck('name', 'id')->toArray(),
                'required' => false
            ]
        ];
    }
    
    protected function getValidationRules(?int $id = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'roles' => 'array'
        ];

        if ($id) {
            $rules['email'] .= '|unique:users,email,' . $id;
        } else {
            $rules['email'] .= '|unique:users,email';
        }

        return $rules;
    }

    public function store(Request $request)
    {
        $this->authorize($this->permissions['store']);
        
        $data = $request->validate($this->getValidationRules());
        
        // Remove roles from data before creating user
        $roles = $data['roles'] ?? [];
        unset($data['roles']);
        
        $user = $this->model::create($data);
        
        // Assign roles if provided
        if (!empty($roles)) {
            $this->accessTreeService->syncUserRoles($user->id, $roles);
        }
        
        return redirect()->route("accesstree.admin.{$this->resourceName}.index")
            ->with('success', ucfirst($this->resourceName) . ' created successfully');
    }
    
    public function update(Request $request, $id)
    {
        $this->authorize($this->permissions['update']);
        
        $user = $this->model::findOrFail($id);
        $data = $request->validate($this->getValidationRules($id));
        
        // Remove roles from data before updating user
        $roles = $data['roles'] ?? [];
        unset($data['roles']);
        
        $user->update($data);
        
        // Sync roles if provided
        if (isset($roles)) {
            $this->accessTreeService->syncUserRoles($user->id, $roles);
        }
        
        return redirect()->route("accesstree.admin.{$this->resourceName}.index")
            ->with('success', ucfirst($this->resourceName) . ' updated successfully');
    }

    public function showRoles($id)
    {
        $this->authorize("view_{$this->resourceName}");
        
        $userModel = $this->model;
        $user = $userModel::with('roles')->findOrFail($id);
        $allRoles = Role::where('status', 1)->get();
        
        return view("accesstree::admin.{$this->resourceName}.roles", [
            'user' => $user,
            'allRoles' => $allRoles,
            'resourceName' => $this->resourceName,
        ]);
    }

    public function syncRoles(Request $request, $id)
    {
        $this->authorize("edit_{$this->resourceName}");
        
        $roleIds = $request->get('roles', []);
        $response = $this->accessTreeService->syncUserRoles($id, $roleIds);
        
        if ($response->isSuccess()) {
            return redirect()->route("accesstree.admin.{$this->resourceName}.show", $id)
                ->with('success', $response->message);
        }
        
        return redirect()->back()
            ->with('error', $response->message);
    }

    public function toggleRootUser($id)
    {
        $this->authorize("edit_{$this->resourceName}");
        
        $userModel = $this->model;
        $user = $userModel::findOrFail($id);
        $user->is_root_user = !$user->is_root_user;
        $user->save();
        
        $status = $user->is_root_user ? 'granted' : 'revoked';
        
        return redirect()->back()
            ->with('success', "Root user access {$status} successfully");
    }
}

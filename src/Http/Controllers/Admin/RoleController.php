<?php

namespace Obrainwave\AccessTree\Http\Controllers\Admin;

use Obrainwave\AccessTree\Models\Role;
use Obrainwave\AccessTree\Models\Permission;
use Obrainwave\AccessTree\Contracts\AccessTreeServiceInterface;
use Illuminate\Http\Request;

class RoleController extends ResourceController
{
    protected $model = Role::class;
    protected $resourceName = 'roles';
    
    public function __construct(
        private AccessTreeServiceInterface $accessTreeService
    ) {
        parent::__construct();
    }
    
    protected function getSearchableFields(): array
    {
        return ['name', 'slug'];
    }
    
    protected function getTableColumns(): array
    {
        return [
            'name' => 'Name',
            'slug' => 'Slug',
            'permissions_count' => 'Permissions',
            'users_count' => 'Users',
            'status' => 'Status',
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
                'placeholder' => 'Enter role name'
            ],
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'options' => [1 => 'Active', 0 => 'Inactive'],
                'required' => true
            ],
            'permissions' => [
                'type' => 'checkbox-group',
                'label' => 'Permissions',
                'options' => Permission::where('status', 1)->pluck('name', 'id')->toArray(),
                'required' => false
            ]
        ];
    }
    
    protected function getValidationRules(?int $id = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'permissions' => 'array'
        ];

        if ($id) {
            $rules['name'] .= '|unique:roles,name,' . $id;
        } else {
            $rules['name'] .= '|unique:roles,name';
        }

        return $rules;
    }

    public function store(Request $request)
    {
        $this->authorize($this->permissions['store']);
        
        $permissionIds = $request->get('permissions', []);
        $response = $this->accessTreeService->createRole($request->only(['name', 'status']), $permissionIds);
        
        if ($response->isSuccess()) {
            return redirect()->route("accesstree.admin.{$this->resourceName}.index")
                ->with('success', $response->message);
        }
        
        return redirect()->back()
            ->withErrors($response->errors)
            ->withInput();
    }

    public function update(Request $request, $id)
    {
        $this->authorize($this->permissions['update']);
        
        $permissionIds = $request->get('permissions', []);
        $response = $this->accessTreeService->updateRole($id, $request->only(['name', 'status']), $permissionIds);
        
        if ($response->isSuccess()) {
            return redirect()->route("accesstree.admin.{$this->resourceName}.index")
                ->with('success', $response->message);
        }
        
        return redirect()->back()
            ->withErrors($response->errors)
            ->withInput();
    }

    public function destroy($id)
    {
        $this->authorize($this->permissions['destroy']);
        
        $response = $this->accessTreeService->deleteRole($id);
        
        if ($response->isSuccess()) {
            return redirect()->route("accesstree.admin.{$this->resourceName}.index")
                ->with('success', $response->message);
        }
        
        return redirect()->back()
            ->with('error', $response->message);
    }

    public function edit($id)
    {
        $this->authorize($this->permissions['edit']);
        
        $item = $this->model::with('permissions')->findOrFail($id);
        $selectedPermissions = $item->permissions->pluck('id')->toArray();
        
        return view("accesstree::admin.{$this->resourceName}.edit", [
            'item' => $item,
            'resourceName' => $this->resourceName,
            'fields' => $this->getFormFields(),
            'selectedPermissions' => $selectedPermissions,
        ]);
    }
}

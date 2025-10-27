<?php

namespace Obrainwave\AccessTree\Http\Controllers\Admin;

use Obrainwave\AccessTree\Models\Permission;
use Obrainwave\AccessTree\Contracts\AccessTreeServiceInterface;
use Illuminate\Http\Request;

class PermissionController extends ResourceController
{
    protected $model = Permission::class;
    protected $resourceName = 'permissions';
    
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
                'placeholder' => 'Enter permission name'
            ],
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'options' => [1 => 'Active', 0 => 'Inactive'],
                'required' => true
            ]
        ];
    }
    
    protected function getValidationRules(?int $id = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'status' => 'required|boolean'
        ];

        if ($id) {
            $rules['name'] .= '|unique:permissions,name,' . $id;
        } else {
            $rules['name'] .= '|unique:permissions,name';
        }

        return $rules;
    }

    public function store(Request $request)
    {
        $this->authorize($this->permissions['store']);
        
        $response = $this->accessTreeService->createPermission($request->all());
        
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
        
        $response = $this->accessTreeService->updatePermission($id, $request->all());
        
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
        
        $response = $this->accessTreeService->deletePermission($id);
        
        if ($response->isSuccess()) {
            return redirect()->route("accesstree.admin.{$this->resourceName}.index")
                ->with('success', $response->message);
        }
        
        return redirect()->back()
            ->with('error', $response->message);
    }
}

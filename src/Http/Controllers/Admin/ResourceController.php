<?php

namespace Obrainwave\AccessTree\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class ResourceController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $model;
    protected $resourceName;
    protected $permissions = [];
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->setPermissions();
    }
    
    protected function setPermissions()
    {
        $this->permissions = [
            'index' => "view_{$this->resourceName}",
            'create' => "create_{$this->resourceName}",
            'store' => "create_{$this->resourceName}",
            'show' => "view_{$this->resourceName}",
            'edit' => "edit_{$this->resourceName}",
            'update' => "edit_{$this->resourceName}",
            'destroy' => "delete_{$this->resourceName}",
        ];
    }
    
    public function index(Request $request)
    {
        $this->authorize($this->permissions['index']);
        
        $query = $this->model::query();
        
        // Auto-search functionality
        if ($request->has('search') && $request->search) {
            $searchableFields = $this->getSearchableFields();
            $query->where(function($q) use ($request, $searchableFields) {
                foreach ($searchableFields as $field) {
                    $q->orWhere($field, 'like', "%{$request->search}%");
                }
            });
        }
        
        // Auto-pagination
        $items = $query->paginate($request->get('per_page', 15));
        
        return view("accesstree::admin.{$this->resourceName}.index", [
            'items' => $items,
            'resourceName' => $this->resourceName,
            'searchableFields' => $this->getSearchableFields(),
            'columns' => $this->getTableColumns(),
        ]);
    }
    
    public function create()
    {
        $this->authorize($this->permissions['create']);
        
        return view("accesstree::admin.{$this->resourceName}.create", [
            'resourceName' => $this->resourceName,
            'fields' => $this->getFormFields(),
        ]);
    }
    
    public function store(Request $request)
    {
        $this->authorize($this->permissions['store']);
        
        $data = $request->validate($this->getValidationRules());
        $item = $this->model::create($data);
        
        return redirect()->route("accesstree.admin.{$this->resourceName}.index")
            ->with('success', ucfirst($this->resourceName) . ' created successfully');
    }

    public function show($id)
    {
        $this->authorize($this->permissions['show']);
        
        $item = $this->model::findOrFail($id);
        
        return view("accesstree::admin.{$this->resourceName}.show", [
            'item' => $item,
            'resourceName' => $this->resourceName,
        ]);
    }
    
    public function edit($id)
    {
        $this->authorize($this->permissions['edit']);
        
        $item = $this->model::findOrFail($id);
        
        return view("accesstree::admin.{$this->resourceName}.edit", [
            'item' => $item,
            'resourceName' => $this->resourceName,
            'fields' => $this->getFormFields(),
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $this->authorize($this->permissions['update']);
        
        $item = $this->model::findOrFail($id);
        $data = $request->validate($this->getValidationRules($id));
        $item->update($data);
        
        return redirect()->route("accesstree.admin.{$this->resourceName}.index")
            ->with('success', ucfirst($this->resourceName) . ' updated successfully');
    }
    
    public function destroy($id)
    {
        $this->authorize($this->permissions['destroy']);
        
        $item = $this->model::findOrFail($id);
        $item->delete();
        
        return redirect()->route("accesstree.admin.{$this->resourceName}.index")
            ->with('success', ucfirst($this->resourceName) . ' deleted successfully');
    }

    // Abstract methods to be implemented by child classes
    abstract protected function getSearchableFields(): array;
    abstract protected function getTableColumns(): array;
    abstract protected function getFormFields(): array;
    abstract protected function getValidationRules(?int $id = null): array;
}

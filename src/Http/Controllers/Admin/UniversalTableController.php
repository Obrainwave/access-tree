<?php

namespace Obrainwave\AccessTree\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UniversalTableController extends Controller
{
    protected $table;
    protected $model;
    protected $columns;
    protected $fillableColumns;
    
    public function __construct()
    {
        // This will be set dynamically based on the route
    }
    
    public function setTable($table)
    {
        if (!$table) {
            throw new \InvalidArgumentException('Table name cannot be empty');
        }
        
        // Validate that the table is in the managed tables list (if configured)
        $this->validateTableAccess($table);
        
        $this->table = $table;
        $this->columns = Schema::getColumnListing($table);
        $this->fillableColumns = array_diff($this->columns, ['id', 'created_at', 'updated_at']);
        
        // Try to find the corresponding model
        $modelName = Str::studly(Str::singular($table));
        $modelClass = "App\\Models\\{$modelName}";
        
        if (class_exists($modelClass)) {
            $this->model = new $modelClass;
        } else {
            // Create a dynamic model for the table
            $this->model = $this->createDynamicModel($table);
        }
    }
    
    /**
     * Validate that the table can be accessed based on configuration
     */
    protected function validateTableAccess($table)
    {
        $managedTables = config('accesstree.managed_tables', []);
        
        // If empty array, all tables are managed
        if (empty($managedTables) || !is_array($managedTables)) {
            return;
        }
        
        // Check if the table is in the managed list
        if (!in_array($table, $managedTables)) {
            abort(403, "Table '{$table}' is not in the managed tables list. Please add it to the 'managed_tables' configuration in accesstree.php.");
        }
    }
    
    public function index(Request $request, $table)
    {
        $this->setTable($table);
        
        $query = DB::table($this->table);
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                foreach ($this->fillableColumns as $column) {
                    $q->orWhere($column, 'like', "%{$searchTerm}%");
                }
            });
        }
        
        // Pagination
        $perPage = $request->get('per_page', 15);
        $items = $query->paginate($perPage);
        
        return view("accesstree::admin.universal.index", [
            $this->table => $items,
            'table' => $this->table,
            'columns' => $this->columns,
            'fillableColumns' => $this->fillableColumns
        ]);
    }
    
    public function create(Request $request, $table)
    {
        $this->setTable($table);
        
        return view("accesstree::admin.universal.form", [
            'table' => $this->table,
            'columns' => $this->columns,
            'fillableColumns' => $this->fillableColumns,
            'item' => null
        ]);
    }
    
    public function store(Request $request, $table)
    {
        $this->setTable($table);
        
        $data = $request->only($this->fillableColumns);
        
        // Add timestamps
        $data['created_at'] = now();
        $data['updated_at'] = now();
        
        $id = DB::table($this->table)->insertGetId($data);
        
        return redirect()
            ->route("accesstree.admin.tables.show", [$table, $id])
            ->with('success', ucfirst(Str::singular($this->table)) . ' created successfully!');
    }
    
    public function show(Request $request, $table, $id)
    {
        $this->setTable($table);
        
        $item = DB::table($this->table)->where('id', $id)->first();
        
        if (!$item) {
            abort(404, ucfirst(Str::singular($this->table)) . ' not found');
        }
        
        return view("accesstree::admin.universal.show", [
            'table' => $this->table,
            'item' => $item,
            'columns' => $this->columns
        ]);
    }
    
    public function edit(Request $request, $table, $id)
    {
        $this->setTable($table);
        
        $item = DB::table($this->table)->where('id', $id)->first();
        
        if (!$item) {
            abort(404, ucfirst(Str::singular($this->table)) . ' not found');
        }
        
        return view("accesstree::admin.universal.form", [
            'table' => $this->table,
            'columns' => $this->columns,
            'fillableColumns' => $this->fillableColumns,
            'item' => $item
        ]);
    }
    
    public function update(Request $request, $table, $id)
    {
        $this->setTable($table);
        
        $data = $request->only($this->fillableColumns);
        $data['updated_at'] = now();
        
        $updated = DB::table($this->table)->where('id', $id)->update($data);
        
        if (!$updated) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update ' . Str::singular($this->table));
        }
        
        return redirect()
            ->route("accesstree.admin.tables.show", [$table, $id])
            ->with('success', ucfirst(Str::singular($this->table)) . ' updated successfully!');
    }
    
    public function destroy(Request $request, $table, $id)
    {
        $this->setTable($table);
        
        $deleted = DB::table($this->table)->where('id', $id)->delete();
        
        if (!$deleted) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete ' . Str::singular($this->table));
        }
        
        return redirect()
            ->route("accesstree.admin.tables.index", $table)
            ->with('success', ucfirst(Str::singular($this->table)) . ' deleted successfully!');
    }
    
    private function createDynamicModel($table)
    {
        return new class($table) {
            protected $table;
            protected $fillable = [];
            
            public function __construct($table)
            {
                $this->table = $table;
                $this->fillable = Schema::getColumnListing($table);
            }
            
            public function getTable()
            {
                return $this->table;
            }
            
            public function getFillable()
            {
                return $this->fillable;
            }
        };
    }
}

<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DiscoverTablesCommand extends Command
{
    protected $signature = 'accesstree:discover-tables 
                            {--exclude= : Comma-separated list of tables to exclude}
                            {--include= : Comma-separated list of tables to include only}
                            {--generate-views : Generate admin views for discovered tables}
                            {--generate-routes : Generate admin routes for discovered tables}';

    protected $description = 'Discover all database tables and generate admin interfaces for them';

    public function handle()
    {
        $this->info('🔍 Discovering database tables...');
        
        $excludeTables = $this->option('exclude') ? explode(',', $this->option('exclude')) : [];
        $includeTables = $this->option('include') ? explode(',', $this->option('include')) : [];
        
        // Get all tables from the database
        $tables = $this->getAllTables();
        
        // Filter tables
        $filteredTables = $this->filterTables($tables, $excludeTables, $includeTables);
        
        $this->info("📊 Found " . count($filteredTables) . " tables:");
        
        foreach ($filteredTables as $table) {
            $this->line("  • {$table}");
        }
        
        // Generate admin interfaces if requested
        if ($this->option('generate-views')) {
            $this->generateAdminViews($filteredTables);
        }
        
        if ($this->option('generate-routes')) {
            $this->generateAdminRoutes($filteredTables);
        }
        
        $this->info('✅ Table discovery completed!');
    }
    
    private function getAllTables()
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");
        
        switch ($driver) {
            case 'mysql':
                $tables = DB::select('SHOW TABLES');
                return array_map(function($table) {
                    return array_values((array)$table)[0];
                }, $tables);
                
            case 'pgsql':
                $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
                return array_map(function($table) {
                    return $table->tablename;
                }, $tables);
                
            case 'sqlite':
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
                return array_map(function($table) {
                    return $table->name;
                }, $tables);
                
            default:
                $this->error("Unsupported database driver: {$driver}");
                return [];
        }
    }
    
    private function filterTables($tables, $excludeTables, $includeTables)
    {
        // Default tables to exclude
        $defaultExclude = [
            'migrations',
            'password_resets',
            'failed_jobs',
            'personal_access_tokens',
            'sessions',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches'
        ];
        
        $excludeTables = array_merge($excludeTables, $defaultExclude);
        
        $filtered = array_filter($tables, function($table) use ($excludeTables, $includeTables) {
            // If include is specified, only include those tables
            if (!empty($includeTables)) {
                return in_array($table, $includeTables);
            }
            
            // Otherwise, exclude specified tables
            return !in_array($table, $excludeTables);
        });
        
        return array_values($filtered);
    }
    
    private function generateAdminViews($tables)
    {
        $this->info('🎨 Generating admin views...');
        
        foreach ($tables as $table) {
            $this->generateTableViews($table);
        }
    }
    
    private function generateAdminRoutes($tables)
    {
        $this->info('🛣️ Generating admin routes...');
        
        $routeContent = "<?php\n\n// Auto-generated routes for discovered tables\n";
        
        foreach ($tables as $table) {
            $controllerName = Str::studly($table) . 'Controller';
            $routeName = Str::kebab($table);
            
            $routeContent .= "// {$table} routes\n";
            $routeContent .= "Route::resource('{$routeName}', {$controllerName}::class);\n";
            $routeContent .= "\n";
        }
        
        file_put_contents(base_path('routes/admin-discovered.php'), $routeContent);
        $this->info('✅ Routes generated in routes/admin-discovered.php');
    }
    
    private function generateTableViews($table)
    {
        $this->line("  📝 Generating views for {$table}...");
        
        // Get table structure
        $columns = Schema::getColumnListing($table);
        
        // Generate index view
        $this->generateIndexView($table, $columns);
        
        // Generate form view
        $this->generateFormView($table, $columns);
        
        // Generate show view
        $this->generateShowView($table, $columns);
    }
    
    private function generateIndexView($table, $columns)
    {
        $viewPath = resource_path("views/admin/{$table}/index.blade.php");
        $viewDir = dirname($viewPath);
        
        if (!is_dir($viewDir)) {
            mkdir($viewDir, 0755, true);
        }
        
        $content = $this->getIndexViewContent($table, $columns);
        file_put_contents($viewPath, $content);
    }
    
    private function generateFormView($table, $columns)
    {
        $viewPath = resource_path("views/admin/{$table}/form.blade.php");
        $viewDir = dirname($viewPath);
        
        if (!is_dir($viewDir)) {
            mkdir($viewDir, 0755, true);
        }
        
        $content = $this->getFormViewContent($table, $columns);
        file_put_contents($viewPath, $content);
    }
    
    private function generateShowView($table, $columns)
    {
        $viewPath = resource_path("views/admin/{$table}/show.blade.php");
        $viewDir = dirname($viewPath);
        
        if (!is_dir($viewDir)) {
            mkdir($viewDir, 0755, true);
        }
        
        $content = $this->getShowViewContent($table, $columns);
        file_put_contents($viewPath, $content);
    }
    
    private function getIndexViewContent($table, $columns)
    {
        $tableName = Str::studly($table);
        $routeName = Str::kebab($table);
        
        return "@extends('accesstree::admin.layouts.app')

@section('title', 'Manage {$tableName}')

@section('content')
<div class=\"container-fluid\">
    <div class=\"row\">
        <div class=\"col-12\">
            <div class=\"card\">
                <div class=\"card-header d-flex justify-content-between align-items-center\">
                    <h3 class=\"card-title\">{$tableName} Management</h3>
                    <a href=\"{{ route('admin.{$routeName}.create') }}\" class=\"btn btn-primary\">
                        <i class=\"fas fa-plus\"></i> Add New {$tableName}
                    </a>
                </div>
                <div class=\"card-body\">
                    <div class=\"table-responsive\">
                        <table class=\"table table-striped\">
                            <thead>
                                <tr>
                                    @foreach(['" . implode("', '", array_slice($columns, 0, 5)) . "'] as \$column)
                                        <th>{{ ucfirst(str_replace('_', ' ', \$column)) }}</th>
                                    @endforeach
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\${$routeName} as \${$routeName}Item)
                                    <tr>
                                        @foreach(['" . implode("', '", array_slice($columns, 0, 5)) . "'] as \$column)
                                            <td>{{ \${$routeName}Item->\$column }}</td>
                                        @endforeach
                                        <td>
                                            <a href=\"{{ route('admin.{$routeName}.show', \${$routeName}Item->id) }}\" class=\"btn btn-sm btn-info\">
                                                <i class=\"fas fa-eye\"></i>
                                            </a>
                                            <a href=\"{{ route('admin.{$routeName}.edit', \${$routeName}Item->id) }}\" class=\"btn btn-sm btn-warning\">
                                                <i class=\"fas fa-edit\"></i>
                                            </a>
                                            <form action=\"{{ route('admin.{$routeName}.destroy', \${$routeName}Item->id) }}\" method=\"POST\" class=\"d-inline\">
                                                @csrf
                                                @method('DELETE')
                                                <button type=\"submit\" class=\"btn btn-sm btn-danger\" onclick=\"return confirm('Are you sure?')\">
                                                    <i class=\"fas fa-trash\"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan=\"6\" class=\"text-center\">No {$tableName} found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection";
    }
    
    private function getFormViewContent($table, $columns)
    {
        $tableName = Str::studly($table);
        $routeName = Str::kebab($table);
        
        $formFields = '';
        foreach ($columns as $column) {
            if (in_array($column, ['id', 'created_at', 'updated_at'])) continue;
            
            $fieldType = $this->getFieldType($column);
            $formFields .= "                    <div class=\"form-group\">\n";
            $formFields .= "                        <label for=\"{$column}\">{{ ucfirst(str_replace('_', ' ', '{$column}')) }}</label>\n";
            $formFields .= "                        <input type=\"{$fieldType}\" class=\"form-control\" id=\"{$column}\" name=\"{$column}\" value=\"{{ old('{$column}', \${$routeName}->{$column} ?? '') }}\">\n";
            $formFields .= "                        @error('{$column}')\n";
            $formFields .= "                            <div class=\"text-danger\">{{ \$message }}</div>\n";
            $formFields .= "                        @enderror\n";
            $formFields .= "                    </div>\n";
        }
        
        return "@extends('accesstree::admin.layouts.app')

@section('title', '{{ isset(\${$routeName}->id) ? 'Edit' : 'Create' }} {$tableName}')

@section('content')
<div class=\"container-fluid\">
    <div class=\"row\">
        <div class=\"col-12\">
            <div class=\"card\">
                <div class=\"card-header\">
                    <h3 class=\"card-title\">{{ isset(\${$routeName}->id) ? 'Edit' : 'Create' }} {$tableName}</h3>
                </div>
                <div class=\"card-body\">
                    <form action=\"{{ isset(\${$routeName}->id) ? route('admin.{$routeName}.update', \${$routeName}->id) : route('admin.{$routeName}.store') }}\" method=\"POST\">
                        @csrf
                        @if(isset(\${$routeName}->id))
                            @method('PUT')
                        @endif
                        
{$formFields}
                        
                        <div class=\"form-group\">
                            <button type=\"submit\" class=\"btn btn-primary\">
                                {{ isset(\${$routeName}->id) ? 'Update' : 'Create' }} {$tableName}
                            </button>
                            <a href=\"{{ route('admin.{$routeName}.index') }}\" class=\"btn btn-secondary\">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection";
    }
    
    private function getShowViewContent($table, $columns)
    {
        $tableName = Str::studly($table);
        $routeName = Str::kebab($table);
        
        $showFields = '';
        foreach ($columns as $column) {
            $showFields .= "                    <div class=\"row mb-3\">\n";
            $showFields .= "                        <div class=\"col-sm-3\"><strong>{{ ucfirst(str_replace('_', ' ', '{$column}')) }}:</strong></div>\n";
            $showFields .= "                        <div class=\"col-sm-9\">{{ \${$routeName}->{$column} }}</div>\n";
            $showFields .= "                    </div>\n";
        }
        
        return "@extends('accesstree::admin.layouts.app')

@section('title', 'View {$tableName}')

@section('content')
<div class=\"container-fluid\">
    <div class=\"row\">
        <div class=\"col-12\">
            <div class=\"card\">
                <div class=\"card-header d-flex justify-content-between align-items-center\">
                    <h3 class=\"card-title\">{$tableName} Details</h3>
                    <div>
                        <a href=\"{{ route('admin.{$routeName}.edit', \${$routeName}->id) }}\" class=\"btn btn-warning\">
                            <i class=\"fas fa-edit\"></i> Edit
                        </a>
                        <a href=\"{{ route('admin.{$routeName}.index') }}\" class=\"btn btn-secondary\">
                            <i class=\"fas fa-arrow-left\"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class=\"card-body\">
{$showFields}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection";
    }
    
    private function getFieldType($column)
    {
        if (str_contains($column, 'email')) return 'email';
        if (str_contains($column, 'password')) return 'password';
        if (str_contains($column, 'phone')) return 'tel';
        if (str_contains($column, 'url')) return 'url';
        if (str_contains($column, 'date')) return 'date';
        if (str_contains($column, 'time')) return 'datetime-local';
        if (str_contains($column, 'description') || str_contains($column, 'content')) return 'textarea';
        
        return 'text';
    }
}

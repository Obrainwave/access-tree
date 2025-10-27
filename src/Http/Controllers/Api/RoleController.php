<?php

namespace Obrainwave\AccessTree\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Obrainwave\AccessTree\Models\Role;
use Obrainwave\AccessTree\Http\Resources\RoleResource;
use Obrainwave\AccessTree\Contracts\AccessTreeServiceInterface;

class RoleController extends Controller
{
    public function __construct(
        private AccessTreeServiceInterface $accessTreeService
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roles = Role::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->with('permissions')
            ->paginate($request->get('per_page', 15));

        return RoleResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $permissionIds = $request->get('permissions', []);
        $response = $this->accessTreeService->createRole($request->only(['name', 'status']), $permissionIds);
        
        if ($response->isSuccess()) {
            return response()->json([
                'success' => true,
                'message' => $response->message,
                'data' => new RoleResource($response->data)
            ], 201);
        }
        
        return response()->json([
            'success' => false,
            'message' => $response->message,
            'errors' => $response->errors
        ], $response->statusCode);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return new RoleResource($role->load('permissions', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $permissionIds = $request->get('permissions', []);
        $response = $this->accessTreeService->updateRole($role->id, $request->only(['name', 'status']), $permissionIds);
        
        if ($response->isSuccess()) {
            return response()->json([
                'success' => true,
                'message' => $response->message,
                'data' => new RoleResource($response->data)
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => $response->message,
            'errors' => $response->errors
        ], $response->statusCode);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $response = $this->accessTreeService->deleteRole($role->id);
        
        if ($response->isSuccess()) {
            return response()->json([
                'success' => true,
                'message' => $response->message
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => $response->message
        ], $response->statusCode);
    }
}

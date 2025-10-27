<?php

namespace Obrainwave\AccessTree\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Obrainwave\AccessTree\Models\Permission;
use Obrainwave\AccessTree\Http\Resources\PermissionResource;
use Obrainwave\AccessTree\Contracts\AccessTreeServiceInterface;

class PermissionController extends Controller
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
        $permissions = Permission::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->paginate($request->get('per_page', 15));

        return PermissionResource::collection($permissions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $response = $this->accessTreeService->createPermission($request->all());
        
        if ($response->isSuccess()) {
            return response()->json([
                'success' => true,
                'message' => $response->message,
                'data' => new PermissionResource($response->data)
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
    public function show(Permission $permission)
    {
        return new PermissionResource($permission->load('roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $response = $this->accessTreeService->updatePermission($permission->id, $request->all());
        
        if ($response->isSuccess()) {
            return response()->json([
                'success' => true,
                'message' => $response->message,
                'data' => new PermissionResource($response->data)
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
    public function destroy(Permission $permission)
    {
        $response = $this->accessTreeService->deletePermission($permission->id);
        
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

<?php

namespace Obrainwave\AccessTree\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Obrainwave\AccessTree\Http\Resources\UserResource;
use Obrainwave\AccessTree\Contracts\AccessTreeServiceInterface;

class UserController extends Controller
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
        $users = User::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->with('roles')
            ->paginate($request->get('per_page', 15));

        return UserResource::collection($users);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new UserResource($user->load('roles'));
    }

    /**
     * Sync user roles.
     */
    public function syncRoles(Request $request, User $user)
    {
        $roleIds = $request->get('roles', []);
        $response = $this->accessTreeService->syncUserRoles($user->id, $roleIds);
        
        if ($response->isSuccess()) {
            return response()->json([
                'success' => true,
                'message' => $response->message,
                'data' => new UserResource($user->fresh()->load('roles'))
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => $response->message,
            'errors' => $response->errors
        ], $response->statusCode);
    }
}

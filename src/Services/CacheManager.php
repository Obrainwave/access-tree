<?php

namespace Obrainwave\AccessTree\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Obrainwave\AccessTree\Repositories\UserRepository;

class CacheManager
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function getUserPermissions(int $userId): array
    {
        return Cache::store(config('accesstree.cache_driver', 'default'))->remember(
            $this->getCacheKey($userId, 'permissions'),
            now()->addMinutes(config('accesstree.cache_refresh_time', 5)),
            function () use ($userId) {
                return $this->fetchUserPermissionsFromDatabase($userId);
            }
        );
    }

    public function getUserRoles(int $userId): array
    {
        return Cache::store(config('accesstree.cache_driver', 'default'))->remember(
            $this->getCacheKey($userId, 'roles'),
            now()->addMinutes(config('accesstree.cache_refresh_time', 5)),
            function () use ($userId) {
                return $this->fetchUserRolesFromDatabase($userId);
            }
        );
    }

    public function clearUserCache(int $userId): void
    {
        Cache::store(config('accesstree.cache_driver', 'default'))->forget($this->getCacheKey($userId, 'permissions'));
        Cache::store(config('accesstree.cache_driver', 'default'))->forget($this->getCacheKey($userId, 'roles'));
    }

    public function clearAllCache(): void
    {
        try {
            Cache::tags(['accesstree'])->flush();
        } catch (\Exception $e) {
            // If tagging is not supported, clear all cache
            Cache::flush();
        }
    }

    public function clearPermissionCache(): void
    {
        try {
            Cache::tags(['accesstree', 'permissions'])->flush();
        } catch (\Exception $e) {
            // If tagging is not supported, clear all cache
            Cache::flush();
        }
    }

    public function clearRoleCache(): void
    {
        try {
            Cache::tags(['accesstree', 'roles'])->flush();
        } catch (\Exception $e) {
            // If tagging is not supported, clear all cache
            Cache::flush();
        }
    }

    private function getCacheKey(int $userId, string $type): string
    {
        return "accesstree_user_{$userId}_{$type}_" . md5($userId . $type);
    }

    private function fetchUserPermissionsFromDatabase(int $userId): array
    {
        return DB::table('user_roles')
            ->join('role_has_permissions', 'user_roles.role_id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->where('user_roles.user_id', $userId)
            ->where('permissions.status', 1)
            ->pluck('permissions.slug')
            ->unique()
            ->toArray();
    }

    private function fetchUserRolesFromDatabase(int $userId): array
    {
        return DB::table('user_roles')
            ->join('roles', 'user_roles.role_id', '=', 'roles.id')
            ->where('user_roles.user_id', $userId)
            ->where('roles.status', 1)
            ->pluck('roles.slug')
            ->unique()
            ->toArray();
    }
}

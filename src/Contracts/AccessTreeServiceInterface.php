<?php

namespace Obrainwave\AccessTree\Contracts;

use Obrainwave\AccessTree\Responses\AccessTreeResponse;

interface AccessTreeServiceInterface
{
    public function createPermission(array $data): AccessTreeResponse;
    public function updatePermission(int $id, array $data): AccessTreeResponse;
    public function deletePermission(int $id): AccessTreeResponse;
    public function createRole(array $data, array $permissionIds = []): AccessTreeResponse;
    public function updateRole(int $id, array $data, array $permissionIds = []): AccessTreeResponse;
    public function deleteRole(int $id): AccessTreeResponse;
    public function assignRoleToUser(int $userId, $role): AccessTreeResponse;
    public function removeRoleFromUser(int $userId, $role): AccessTreeResponse;
    public function syncUserRoles(int $userId, array $roles): AccessTreeResponse;
    public function getUserPermissions(int $userId): array;
    public function getUserRoles(int $userId): array;
    public function checkPermission(string $permission, ?int $userId = null): bool;
    public function checkPermissions(array $permissions, bool $strict = false, ?int $userId = null): bool;
    public function checkRole(string $role, ?int $userId = null): bool;
    public function checkRoles(array $roles, bool $strict = false, ?int $userId = null): bool;
}

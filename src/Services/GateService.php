<?php

namespace Obrainwave\AccessTree\Services;

use Illuminate\Support\Facades\Gate;
use Obrainwave\AccessTree\Contracts\AccessTreeServiceInterface;

class GateService
{
    public function __construct(
        private AccessTreeServiceInterface $accessTreeService
    ) {}

    public function registerGates(): void
    {
        // Register a global before callback
        Gate::before(function ($user, $ability) {
            if (!$user) {
                return false;
            }

            // Check if user is root user
            if ($this->accessTreeService->checkRole('root', $user->id)) {
                return true;
            }

            // Check permission using AccessTree
            return $this->accessTreeService->checkPermission($ability, $user->id);
        });

        // Register specific gates for common operations
        $this->registerCommonGates();
    }

    protected function registerCommonGates(): void
    {
        // User management gates
        Gate::define('manage-users', function ($user) {
            return $this->accessTreeService->checkPermission('manage_users', $user->id);
        });

        Gate::define('view-users', function ($user) {
            return $this->accessTreeService->checkPermission('view_users', $user->id);
        });

        Gate::define('create-users', function ($user) {
            return $this->accessTreeService->checkPermission('create_users', $user->id);
        });

        Gate::define('edit-users', function ($user) {
            return $this->accessTreeService->checkPermission('edit_users', $user->id);
        });

        Gate::define('delete-users', function ($user) {
            return $this->accessTreeService->checkPermission('delete_users', $user->id);
        });

        // Role management gates
        Gate::define('manage-roles', function ($user) {
            return $this->accessTreeService->checkPermission('manage_roles', $user->id);
        });

        Gate::define('view-roles', function ($user) {
            return $this->accessTreeService->checkPermission('view_roles', $user->id);
        });

        Gate::define('create-roles', function ($user) {
            return $this->accessTreeService->checkPermission('create_roles', $user->id);
        });

        Gate::define('edit-roles', function ($user) {
            return $this->accessTreeService->checkPermission('edit_roles', $user->id);
        });

        Gate::define('delete-roles', function ($user) {
            return $this->accessTreeService->checkPermission('delete_roles', $user->id);
        });

        // Permission management gates
        Gate::define('manage-permissions', function ($user) {
            return $this->accessTreeService->checkPermission('manage_permissions', $user->id);
        });

        Gate::define('view-permissions', function ($user) {
            return $this->accessTreeService->checkPermission('view_permissions', $user->id);
        });

        Gate::define('create-permissions', function ($user) {
            return $this->accessTreeService->checkPermission('create_permissions', $user->id);
        });

        Gate::define('edit-permissions', function ($user) {
            return $this->accessTreeService->checkPermission('edit_permissions', $user->id);
        });

        Gate::define('delete-permissions', function ($user) {
            return $this->accessTreeService->checkPermission('delete_permissions', $user->id);
        });
    }

    public function registerCustomGate(string $name, string $permission): void
    {
        Gate::define($name, function ($user) use ($permission) {
            return $this->accessTreeService->checkPermission($permission, $user->id);
        });
    }

    public function registerMultipleGates(array $gates): void
    {
        foreach ($gates as $name => $permission) {
            $this->registerCustomGate($name, $permission);
        }
    }
}

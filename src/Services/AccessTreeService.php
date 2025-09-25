<?php
namespace Obrainwave\AccessTree\Services;

use Obrainwave\AccessTree\Traits\AccessOperations;

class AccessTreeService
{
    use AccessOperations;

    /**
     * Create access permissions
     */
    public function createAccess(array $data, string $model, array $permission_ids = []): string
    {
        $model = ucfirst($model);

        switch ($model) {
            case 'Permission':
                return $this->createPermission($data);

            case 'Role':
                return $this->createRole($data, $permission_ids);

            default:
                return json_encode(['status' => 404, 'message' => 'Unknown `' . $model . '` model!']);
        }
    }

    /**
     * Update access permissions
     */
    public function updateAccess(array $data, string $model, array $permission_ids = []): string
    {
        $model = ucfirst($model);

        switch ($model) {
            case 'Permission':
                return $this->updatePermission($data);

            case 'Role':
                return $this->updateRole($data, $permission_ids);

            default:
                return json_encode(['status' => 404, 'message' => 'Unknown `' . $model . '` model!']);
        }
    }

    /**
     * Delete access permissions or roles
     */
    public function deleteAccess(int $data_id, string $model): string
    {
        $model = ucfirst($model);

        switch ($model) {
            case 'Permission':
                return $this->deletePermission($data_id);

            case 'Role':
                return $this->deleteRole($data_id);

            default:
                return json_encode(['status' => 404, 'message' => 'Unknown `' . $model . '` model!']);
        }
    }

}

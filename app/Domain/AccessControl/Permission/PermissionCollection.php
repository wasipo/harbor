<?php

declare(strict_types=1);

namespace App\Domain\AccessControl\Permission;

use App\Domain\Shared\Collections\AbstractEntityCollection;

/**
 * @extends AbstractEntityCollection<Permission>
 */
final class PermissionCollection extends AbstractEntityCollection
{
    /**
     * Permissionの識別子はIDで判定
     * @param Permission $entity
     */
    protected function getIdentifier($entity): string
    {
        return $entity->id->toString();
    }

    /**
     * PermissionIdCollectionを取得
     */
    public function toIds(): PermissionIdCollection
    {
        $ids = [];
        foreach ($this->all() as $permission) {
            $ids[] = $permission->id;
        }
        return new PermissionIdCollection($ids);
    }
}
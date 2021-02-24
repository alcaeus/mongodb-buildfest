<?php

namespace App\MongoDB;

use function class_exists;
use function is_a;
use function is_string;

final class TypeMapGenerator
{
    public static function expandFieldPaths(array $typeMap): array
    {
        if (!isset($typeMap['fieldPaths'])) {
            return $typeMap;
        }

        $additionalFieldPaths = [];

        foreach ($typeMap['fieldPaths'] as $fieldPath => $mapping) {
            if (!is_string($mapping) || !class_exists($mapping)) {
                continue;
            }

            if (!is_a($mapping, TypeMapAware::class, true)) {
                continue;
            }

            $nestedTypeMap = $mapping::getTypeMap();
            if (!isset($nestedTypeMap['fieldPaths'])) {
                continue;
            }

            foreach ($nestedTypeMap['fieldPaths'] as $nestedFieldPath => $nestedMapping) {
                $additionalFieldPaths[$fieldPath . '.' . $nestedFieldPath] = $nestedMapping;
            }
        }

        $typeMap['fieldPaths'] = array_merge($typeMap['fieldPaths'], $additionalFieldPaths);

        return $typeMap;
    }
}

<?php

namespace Differ\Tree;

/**
 * Creates node - a subtree.
 *
 * @param String $name Name of the node
 * @param Array $children Set of new data - nodes, leaves or nested
 * @return Array
 */
function createNode(string $name, array $children): array
{
    return ['name' => $name, 'type' => 'node', 'children' => $children];
}

/**
 * Creates nested - an element of tree with changed value.
 *
 * @param String $name Name of the node
 * @param Mixed $childrenOld Old value
 * @param Mixed $childrenNew New value
 * @return Array
 */
function createNested(string $name, mixed $childrenOld, mixed $childrenNew): array
{
    return ['name' => $name, 'type' => 'nested', 'children' => ['deleted' => $childrenOld, 'added' => $childrenNew]];
}

/**
 * Creates leaf - an element of tree with scalar or unchanged value.
 *
 * @param String $name Name of the leaf
 * @param String $status Status of the leaf - added/deleted/unchanged
 * @param Mixed $value Value of the leaf
 * @return Array
 */
function createLeaf(string $name, string $status, mixed $value): array
{
    return ['name' => $name, 'type' => 'leaf', 'status' => $status, 'value' => $value];
}

/**
 * Returns name of a node/leaf.
 *
 * @param Array $node Variable that contains a node/leaf
 * @return String
 */
function getName(array $node): string
{
    return $node['name'];
}

/**
 * Returns type of a node/leaf.
 *
 * @param Array $node Variable that contains a node/leaf
 * @return String
 */
function getTypeNode(array $node): string
{
    return $node['type'];
}

/**
 * Returns value of a leaf.
 *
 * @param Array $leaf Variable that contains a leaf
 * @return Mixed
 */
function getValueLeaf(array $leaf): mixed
{
    return $leaf['value'];
}

/**
 * Returns status of a leaf.
 *
 * @param Array $leaf Variable that contains a leaf
 * @return String
 */
function getStatusLeaf(array $leaf): string
{
    return $leaf['status'];
}

/**
 * Returns array of children of a node.
 *
 * @param Array $node Variable that contains a node
 * @return Array
 */
function getChildrenNode(array $node): array
{
    return $node['children'];
}

/**
 * Returns array of children of a nested.
 *
 * @param Array $nested Variable that contains a leaf
 * @return Array
 */
function getChildrenNested(array $nested): array
{
    return $nested['children'];
}

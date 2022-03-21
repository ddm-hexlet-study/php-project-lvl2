<?php

namespace Differ\Tree;

function createNode(string $name, mixed $children)
{
    return ['name' => $name, 'type' => 'node', 'children' => $children];
}

function createNested(string $name, mixed $childrenOld, mixed $childrenNew)
{
    return ['name' => $name, 'type' => 'nested', 'children' => ['deleted' => $childrenOld, 'added' => $childrenNew]];
}

function createLeaf(string $name, string $status, mixed $value)
{
    return ['name' => $name, 'type' => 'leaf', 'status' => $status, 'value' => $value];
}

function getName(array $node)
{
    return $node['name'];
}

function getTypeNode(array $node)
{
    return $node['type'];
}

function getValueLeaf(array $node)
{
    return $node['value'];
}

function getStatusLeaf(array $node)
{
    return $node['status'];
}

function getChildrenNode(array $node)
{
    return $node['children'];
}

function getChildrenNested(array $node)
{
    return $node['children'];
}

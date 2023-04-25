<?php
namespace Youshido\GraphQL\Execution\Container;

interface ContainerInterface
{
    public function get(string $id): mixed;
    public function set(string $id, mixed $value): mixed;
    public function remove(string $id): void;
    public function has(string $id): mixed;

}

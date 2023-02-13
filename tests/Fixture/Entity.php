<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Tests\Fixture;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;

#[\Doctrine\ORM\Mapping\Entity]
class Entity
{
    public function __construct(
        #[Id]
        #[Column(type: 'integer')]
        private readonly int $id,

        #[Column(length: 140)]
        public readonly string $value,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }
}
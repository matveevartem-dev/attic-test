<?php

declare(strict_types=1);

namespace App\Model\Post;

use App\Infrastructure\Core\ModelInterface;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\DBAL\Types\Types;

#[Entity]
#[Table(name: 'post')]
final class Post implements ModelInterface
{
    /**
     * @var int
     */
    #[Id]
    #[Column(type: Types::INTEGER)]
    private int $id;

    /**
     * @var int
     */
    #[Column(type: Types::INTEGER, name: 'user_id')]
    private int $userId;

    /**
     * @var string
     */
    #[Column(type: Types::STRING)]
    private string $title;

    /**
     * @var string
     */
    #[Column(type: Types::TEXT)]
    private string $body;

    /**
     * @param int $id
     * @param int $userId
     * @param string $title
     * @param string $body
     */
    public function __construct(int $userId, int $id, string $title, string $body)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return quotemeta(stripslashes($this->title));
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return quotemeta(stripslashes($this->body));
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'userId' => $this->getUserId(),
            'title' => $this->getTitle(),
            'body' => $this->getBody(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __sleep(): array
    {
        return ['id', 'userId', 'title', 'body'];
    }
}

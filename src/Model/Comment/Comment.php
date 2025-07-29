<?php

declare(strict_types=1);

namespace App\Model\Comment;

use App\Infrastructure\Core\ModelInterface;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\DBAL\Types\Types;
use JsonSerializable;

#[Entity]
#[Table(name: 'comment')]
final class Comment implements ModelInterface
{
    /**
     * @var int
     */
    #[Id]
    #[Column(type: Types::INTEGER)]
    private ?int $id;

    /**
     * @var int
     */
    #[Column(type: Types::INTEGER, name: 'post_id')]
    private ?int $postId;

    /**
     * @var string
     */
    #[Column(type: Types::STRING)]
    private ?string $email;

    /**
     * @var string
     */
    #[Column(type: Types::STRING)]
    private ?string $name;

    /**
     * @var string
     */
    #[Column(type: Types::TEXT)]
    private ?string $body;

    /**
     * @param int $postId
     * @param int $id
     * @param string $email
     * @param string $name
     * @param string $body
     */
    public function __construct(int $postId, int $id, string $email, string $name, string $body)
    {
        $this->id = $id;
        $this->postId = $postId;
        $this->email = $email;
        $this->name = $name;
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
    public function getPostId(): int
    {
        return $this->postId;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'postId' => $this->postId,
            'email' => $this->email,
            'name' => $this->name,
            'body' => $this->body,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __sleep(): array
    {
        return ['id', 'postId', 'email', 'title', 'body'];
    }
}

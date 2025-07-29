<?php

declare(strict_types=1);

namespace App\Model\Search;

use JsonSerializable;
use stdClass;

final class SearchResult implements JsonSerializable
{
    /**
     * @param int $id
     * @param int $postId
     * @param string $title
     * @param string $body
     */

    public function __construct(
        private int $id,
        private int $postId,
        private string $email,
        private string $name,
        private string $body
    ) {
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
    public function getTitle(): string
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

    public static function fromObject(stdClass $object): self
    {
        foreach (['id', 'postId', 'email','name','body'] as $key) {
            if (!property_exists($object, $key)) {
                throw new \InvalidArgumentException(sprintf('%s', $key));
            }
        }

        return new self(
                $object->id,
                $object->postId,
                $object->email,
                $object->name,
                $object->body
            );
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
            'title' => $this->name,
            'body' => $this->body,
        ];
    }
}

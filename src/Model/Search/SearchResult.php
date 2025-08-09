<?php

declare(strict_types=1);

namespace App\Model\Search;

use App\Model\Comment\Comment;
use JsonSerializable;
use stdClass;

final class SearchResult implements JsonSerializable
{
    /**
     * @param int $id
     * @param int $title
     * @param string $email
     * @param Comment[] $comments
     */

    public function __construct(
        private int $id,
        private string $title,
        private string $email,
        private array $comments,
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
    public function getTitle(): int
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return Comment[]
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    /**
     * Creates SearchResult from stdClass
     * @param \stdClass $object
     * @throws \InvalidArgumentException
     * @return SearchResult
     */
    public static function fromObject(stdClass $object): self
    {
        foreach (['pid', 'email', 'title', 'comments'] as $key) {
            if (!property_exists($object, $key)) {
                throw new \InvalidArgumentException(sprintf('%s', $key));
            }
        }

        $comments = [];
        foreach (json_decode($object->comments, true) as $comment) {
            $comments[] = Comment::fromArray(array_merge(['pid' => $object->pid], $comment));
        }

        return new self(
                $object->pid,
                $object->title,
                $object->email,
                $comments,
            );
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'email' => $this->email,
            'comments' => $this->comments,
        ];
    }
}

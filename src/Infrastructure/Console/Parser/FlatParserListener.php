<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Parser;

use JsonStreamingParser\Listener\ListenerInterface;

final class FlatParserListener implements ListenerInterface
{
    const BATCH_SIZE = 500;

    private array $objects = [];

    /**
     * @var \stdClass will hold the current object being parsed as an associative array
     */
    private $currentObject;

    /**
     * @var string|null will hold the current key used to feed
     */
    private $currentKey;

    private int $counter = 0;

    /**
     * @var callable|null callback function
     */
    private $callback;

    /**
     * @param string $className which return class to provide
     */
    public function __construct(private string $className, callable $callback)
    {
        $this->callback = $callback;
    }

    public function targetObject()
    {
        try {
            $reflection = new \ReflectionClass($this->className);
        } catch (\ReflectionException $e) {
            throw new \JsonException(sprintf('Unable to create object `%s`.', $this->className), 0, $e);
        }

        if (($constructor = $reflection->getConstructor()) === null) {
            return $reflection->newInstance();
        }

        $arguments = [];

        foreach ($constructor->getParameters() as $parameter) {
            if (property_exists($this->currentObject, $parameter->getName())) {
                $arguments[] = $this->currentObject->{$parameter->getName()};
            }
        }

        try {
            $targetObject =  $reflection->newInstanceArgs($arguments);
            //new $this->className(...$arguments);
        } catch (\InvalidArgumentException $e) {
             throw new \JsonException($e->getMessage());
        }

        return $targetObject;
    }

    public function startDocument(): void
    {
        $this->reset();
    }

    public function endDocument(): void
    {
        $this->makeCallback();
        $this->reset();
    }

    public function startObject(): void
    {
        $this->reset();
    }

    public function endObject(): void
    {
        $this->counter++;
        $this->objects[] = $this->targetObject();

        if($this->counter % self::BATCH_SIZE === 0) {
            $this->makeCallback();
        }

        $this->reset();
        //throw new \Exception('Unsupported callback data type requested.');
    }

    public function startArray(): void
    {
    }

    public function endArray(): void
    {
    }

    public function key(string $key): void
    {
        $this->currentKey = $key;
    }

    public function value($value): void
    {
        $this->currentObject->{$this->currentKey} = $value;
    }

    public function whitespace(string $whitespace): void
    {
    }

    /**
     * Reset all the values to default.
     */
    private function reset(): void
    {
        $this->currentObject = new \stdClass();
        $this->currentKey = null;
    }

    private function makeCallback()
    {
        ($this->callback)($this->objects);
        $this->objects = [];
        $this->counter = 0;
    }
}

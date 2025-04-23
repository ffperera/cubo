<?php

declare(strict_types=1);

namespace FFPerera\Cubo;


class View
{

    private string $layout;

    /**
     * @var array<string, mixed> $templates
     */
    private array $templates;

    /**
     * @var array<string, string> $headers
     */
    private array $headers;

    /**
     * @var array<string, mixed> $bag
     */
    private array $bag;

    public function __construct()
    {
        $this->layout = '';
        $this->templates = [];
        $this->headers = [];
        $this->bag = [];
    }
    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }
    public function getLayout(): string
    {
        return $this->layout;
    }
    public function setTemplate(string $name, string $template): void
    {
        $this->templates[$name] = $template;
    }
    public function getTemplate(string $name): string|null
    {
        return $this->templates[$name] ?? null;
    }
    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function set(string $key, mixed $value): void
    {
        $this->bag[$key] = $value;
    }

    public function get(string $key): mixed
    {
        return $this->bag[$key] ?? null;
    }

    public function isset(string $key): bool
    {
        return isset($this->bag[$key]);
    }

    public function has(string $key): bool
    {
        return isset($this->bag[$key]);
    }

    public function remove(string $key): void
    {
        unset($this->bag[$key]);
    }

    public function clear(): void
    {
        $this->bag = [];
    }

    /**
     * Returns all keys in the bag.
     * 
     * @return array<string>
     */
    public function getAll(): array
    {
        return array_keys($this->bag);
    }
}

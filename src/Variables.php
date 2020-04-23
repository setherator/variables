<?php

declare(strict_types=1);

namespace Setherator\Variables;

use Closure;
use RuntimeException;

/**
 * @author Aurimas Niekis <aurimas@niekis.lt>
 */
class Variables
{
    protected static ?Variables $instance;
    protected array             $variables;
    protected array             $context;

    /** @var ValueParserInterface[] */
    protected array            $valueParsers;

    public function __construct()
    {
        static::$instance = $this;

        $this->variables    = [];
        $this->context      = [];
        $this->valueParsers = [];
    }

    public function add(...$variables): self
    {
        $this->variables = array_replace($this->variables, ...$variables);

        return $this;
    }

    public function addValueParser(ValueParserInterface $valueParser): self
    {
        $this->valueParsers[] = $valueParser;

        return $this;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): self
    {
        $this->context = $context;

        return $this;
    }

    public static function getInstance(): self
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        throw new RuntimeException(static::class . ' was not initialised yet');
    }

    public function all(bool $compute = true): array
    {
        if (false === $compute) {
            return $this->variables;
        }

        $result = [];
        foreach ($this->variables as $name => $value) {
            $result[$name] = $this->get($name);
        }

        return $result;
    }

    public function names(): array
    {
        return array_keys($this->variables);
    }

    public function set(string $key, $value): self
    {
        $this->variables[$key] = $value;

        return $this;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->variables);
    }

    public function remove(string $key): self
    {
        unset($this->variables[$key]);

        return $this;
    }

    public function getRaw(string $name, $default = null)
    {
        if (false === isset($this->variables[$name])) {
            return $default;
        }

        return $this->variables[$name];
    }

    public function get(string $name, $default = null)
    {
        if (false === isset($this->variables[$name])) {
            return $this->parseValue($default);
        }

        $value = $this->variables[$name];

        if ($value instanceof NonCacheableClosure) {
            return $this->parseValue($value);
        }

        $this->variables[$name] = $this->parseValue($value);

        return $this->variables[$name];
    }

    public function parseValue($value)
    {
        if ($value instanceof Closure || $value instanceof NonCacheableClosure) {
            return $this->parseValue($value());
        }

        foreach ($this->valueParsers as $valueParser) {
            if (true === $valueParser->supports($value)) {
                return $this->parseValue(
                    $valueParser->parseValue($value, $this)
                );
            }
        }

        return $value;
    }
}

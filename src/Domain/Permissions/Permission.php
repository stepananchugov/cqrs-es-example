<?php

declare(strict_types=1);

namespace App\Domain\Permissions;

use App\Domain\Permissions\Exception\InvalidArgumentException;
use EventSauce\EventSourcing\Serialization\SerializablePayload;
use Symfony\Component\Messenger\Envelope;

final class Permission implements SerializablePayload
{
    public const PERMISSION_ALL = '*';
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }

    public static function envelopeToPermission(Envelope $envelope): string
    {
        return self::classnameToPermission(\get_class($envelope->getMessage()));
    }

    public static function classnameToPermission(string $classname): string
    {
        if ('' === $classname) {
            throw InvalidArgumentException::invalidClassname($classname);
        }

        $cleanClassname = str_replace(
            ['App\\Application\\', '\\', 'Handler', 'Query', 'Command'],
            ['', '.', '', '', ''],
            $classname
        );

        $parts = array_filter(explode('.', $cleanClassname));
        $result = [];

        foreach ($parts as $part) {
            $str = preg_replace('/[A-Z]/', '_\\0', lcfirst($part));

            if (null === $str || '' === $str) {
                throw InvalidArgumentException::invalidClassname($classname);
            }

            $result[] = strtolower($str);
        }

        return strtolower(implode('.', array_filter($result)));
    }

    public static function fromClassname(string $classname): self
    {
        if (!class_exists($classname)) {
            throw InvalidArgumentException::missingPermissionClass($classname);
        }

        $name = self::classnameToPermission($classname);

        return new self($name);
    }

    public function toPayload(): array
    {
        return ['name' => $this->name];
    }

    /**
     * @return Permission
     */
    public static function fromPayload(array $payload): self
    {
        return new self($payload['name']);
    }
}

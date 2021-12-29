<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class <?php echo $class_name; ?> implements SerializablePayload
{
    // Queries should be immutable:
    /*
    public function withProperty(object $property): self
    {
        $clone = clone $this;
        $clone->eventId = $eventId;

        return $clone;
    }
    */

    public function toPayload(): array
    {
        return [];
    }

    public static function fromPayload(array $payload): self
    {
        return new self();
    }
}

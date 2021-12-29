<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use EventSauce\EventSourcing\Serialization\SerializablePayload;
use Symfony\Component\Validator\Constraints as Assert;

final class <?php echo $class_name; ?> implements SerializablePayload
{
    /**
     * @api
     */
    public function __construct()
    {
        // TODO: Implement your command constructor
    }

    // Commands should be immutable:
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
        $instance = new self();

        return $instance;
    }
}

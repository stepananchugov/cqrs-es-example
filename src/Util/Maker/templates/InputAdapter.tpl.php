<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use App\Ui\GraphQL\Adapter\Exception\InvalidArgumentException;
use App\Ui\GraphQL\Mapping\InputObject\<?php echo $input_object_name; ?>;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class <?php echo $class_name; ?> implements InputAdapterInterface
{
    public function getInputClass(): string
    {
        return <?php echo $input_object_name; ?>::class;
    }

    public function transform(object $input): SerializablePayload
    {
        if (!$input instanceof <?php echo $input_object_name; ?>) {
            throw InvalidArgumentException::inputTypeMismatch($input, <?php echo $input_object_name; ?>::class);
        }

        // return new Command...;
    }
}

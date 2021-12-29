<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

final class <?php echo $class_name."\n"; ?>
{
    public function __invoke(<?php echo $query_class_name; ?> $query): array
    {
        // TODO: Implement the handler
    }
}

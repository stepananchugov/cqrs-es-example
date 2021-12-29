<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use EventSauce\EventSourcing\ConstructingAggregateRootRepository;

final class <?php echo $class_name."\n"; ?>
{
    private ConstructingAggregateRootRepository $repository;

    // TODO: Use the correct aggregate root repository:
    public function __construct(ConstructingAggregateRootRepository $repository)
    {
        $this->repository = $eventRepository;
    }

    public function __invoke(<?php echo $command_name; ?> $command): void
    {
        /** @var \EventSauce\EventSourcing\AggregateRoot $aggregate */

        $this->repository->persist($aggregate);
    }
}

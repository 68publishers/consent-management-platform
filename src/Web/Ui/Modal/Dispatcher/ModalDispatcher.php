<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal\Dispatcher;

use App\Web\Ui\Modal\AbstractModalControl;
use App\Web\Ui\Modal\Dispatcher\Event\ModalClosedEvent;
use App\Web\Ui\Modal\Dispatcher\Event\ModalDispatchedEvent;
use InvalidArgumentException;
use Nette\ComponentModel\IComponent;
use SixtyEightPublishers\EventDispatcherExtra\EventDispatcherAwareTrait;

final class ModalDispatcher implements ModalDispatcherInterface
{
    use EventDispatcherAwareTrait;

    private array $payload = [
        'modals_to_show' => [],
        'modals_to_hide' => [],
    ];

    public function dispatch(IComponent $modal, array $metadata = []): void
    {
        if (!$modal instanceof AbstractModalControl) {
            throw new InvalidArgumentException(sprintf(
                'Modal component must be inheritor of the class %s.',
                AbstractModalControl::class,
            ));
        }

        $name = $modal->getUniqueId();
        $this->payload['modals_to_show'][$name] = [
            'metadata' => $metadata,
            'content' => $modal,
        ];

        $this->dispatchEvent(new ModalDispatchedEvent($name));
    }

    public function close(array $componentNames = []): void
    {
        $componentNames = !empty($componentNames) ? $componentNames : ['*'];
        $componentNames = array_merge($this->payload['modals_to_hide'], $componentNames);

        $this->payload['modals_to_hide'] = in_array('*', $componentNames, true) ? ['*'] : $componentNames;

        $this->dispatchEvent(new ModalClosedEvent($componentNames));
    }

    public function jsonSerialize(): array
    {
        $payload = $this->payload;
        $payload['modals_to_show'] = array_map(static function (array $def): array {
            $def['content'] = $def['content']->toString();

            return $def;
        }, $payload['modals_to_show']);

        return $payload;
    }
}

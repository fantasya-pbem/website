<?php
declare(strict_types = 1);
namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

use App\Service\TempDirService;

#[AsEventListener(event: TerminateEvent::class, method: 'onTerminateEvent')]
final readonly class TempDirListener
{
	public function __construct(private TempDirService $service) {
	}

	public function onTerminateEvent(): void
	{
		$this->service->clean();
	}
}

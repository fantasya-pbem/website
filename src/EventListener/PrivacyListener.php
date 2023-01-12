<?php
declare(strict_types = 1);
namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Controller\PrivacyController;
use App\Service\PrivacyService;

#[AsEventListener(event: ControllerEvent::class, method: 'onControllerEvent')]
final readonly class PrivacyListener
{
	public function __construct(private PrivacyService $service, private PrivacyController $controller) {
	}

	public function onControllerEvent(ControllerEvent $event): void
	{
		if ($this->service->hasAcceptedDsgvo()) {
			return;
		}

		$route = null;
		$role = null;
		foreach ($event->getAttributes() as $attributes) {
			foreach ($attributes as $attribute) {
				if ($attribute instanceof Route) {
					$route = $attribute->getName();
				} elseif ($attribute instanceof IsGranted) {
					$role = $attribute->attribute;
				}
			}
		}

		if ($role && $route) {
			$this->service->setReturn($route);
			$event->setController($this->controller->askAction(...));
		}
	}
}

<?php
declare(strict_types = 1);
namespace App\Service;

use App\Controller\PrivacyController;

class PrivacyService
{
	private string $return = '';

	public function hasAcceptedDsgvo(): bool {
		return isset($_COOKIE[PrivacyController::COOKIE]);
	}

	public function getReturn(): string {
		return $this->return;
	}

	public function setReturn(string $return): void {
		$this->return = $return;
	}
}

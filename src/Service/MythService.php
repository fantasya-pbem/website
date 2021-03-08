<?php
declare (strict_types = 1);
namespace App\Service;

use App\Repository\MythRepository;

class MythService
{
	public function __construct(private MythRepository $repository) {
	}

	public function getLatest(): string {
		$myth = $this->repository->getLatest();
		return $myth ? $myth->getMyth() : '';
	}
}

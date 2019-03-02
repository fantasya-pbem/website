<?php
declare (strict_types = 1);
namespace App\Service;

use App\Repository\MythRepository;

/**
 * A service for templates to fetch myths.
 */
class MythService
{
	/**
	 * @var MythRepository
	 */
	private $repository;

	/**
	 * @param MythRepository $repository
	 */
	public function __construct(MythRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * Get the latest myth.
	 *
	 * @return string
	 */
	public function getLatest(): string {
		$myth = $this->repository->getLatest();
		return $myth ? $myth->getMyth() : '';
	}
}

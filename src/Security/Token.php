<?php
declare (strict_types = 1);
namespace App\Security;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Token
{
	/**
	 * @var string
	 */
	private $secret;

	/**
	 * @var string
	 */
	private $email;

	/**
	 * @var int
	 */
	private $turn;

	/**
	 * Initialize secret.
	 */
	public function __construct() {
		$tree   = new TreeBuilder('kernel');
		$secret = $tree->buildTree()->getPath('kernel.secret');

	}
}

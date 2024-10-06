<?php
declare(strict_types = 1);
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SitemapExtension extends AbstractExtension
{
	public function getFunctions(): array {
		return [
			new TwigFunction('tableMod', [SitemapRuntime::class, 'tableMod']),
			new TwigFunction('templateMod', [SitemapRuntime::class, 'templateMod']),
			new TwigFunction('turnMod', [SitemapRuntime::class, 'turnMod'])
		];
	}
}

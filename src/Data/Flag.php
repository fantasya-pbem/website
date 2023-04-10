<?php
declare(strict_types = 1);
namespace App\Data;

enum Flag: int
{
	case WithAttachment = 1;

	case HtmlReport = 256;

	case TextReport = 512;

	case MagellanReport = 1024;

	case All = 1 + 256 + 512 + 1024;
}

<?php
declare(strict_types = 1);
namespace App\Security;

final class Role
{
	public final const ADMIN = 'ROLE_ADMIN';

	public final const BETA_TESTER = 'ROLE_BETA_TESTER';

	public final const MULTI_PLAYER = 'ROLE_MULTI_PLAYER';

	public final const NEWS_CREATOR = 'ROLE_NEWS_CREATOR';

	public final const USER = 'ROLE_USER';
}

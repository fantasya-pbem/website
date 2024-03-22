<?php
declare(strict_types = 1);
namespace App\Security;

final class Role
{
	public final const string ADMIN = 'ROLE_ADMIN';

	public final const string BETA_TESTER = 'ROLE_BETA_TESTER';

	public final const string MULTI_PLAYER = 'ROLE_MULTI_PLAYER';

	public final const string NEWS_CREATOR = 'ROLE_NEWS_CREATOR';

	public final const string USER = 'ROLE_USER';
}

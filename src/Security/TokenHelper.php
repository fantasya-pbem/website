<?php
declare(strict_types = 1);
namespace App\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use App\Security\X509\ClientCertificate;

class TokenHelper
{
	private const string CERTIFICATE = 'certificate';

	public function __construct(protected readonly ?Security $security = null) {
	}

	public function registerClientCertificate(TokenInterface $token): TokenInterface
	{
		$token->setAttribute(self::CERTIFICATE, new ClientCertificate());
		return $token;
	}

	public function getClientCertificate(): ?ClientCertificate {
		if (!$this->security) {
			return null;
		}
		$token = $this->security->getToken();
		return $token->hasAttribute(self::CERTIFICATE) ? $token->getAttribute(self::CERTIFICATE) : null;
	}
}

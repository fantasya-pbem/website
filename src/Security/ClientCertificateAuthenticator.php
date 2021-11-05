<?php
declare (strict_types = 1);
namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\User;

/**
 * @todo This class is not used anymore and must be reimplemented.
 * @see https://symfony.com/doc/current/security/custom_authenticator.html
 */
class ClientCertificateAuthenticator extends AbstractGuardAuthenticator
{
	use TargetPathTrait;

	public function __construct(private EntityManagerInterface $entityManager, private UrlGeneratorInterface $urlGenerator) {
	}

	public function supports(Request $request): bool {
		return $request->attributes->get('_route') === 'user_secure' && $request->isMethod('GET');
	}

	#[Pure] public function getCredentials(Request $request): ClientCertificate {
		return new ClientCertificate();
	}

	/**
	 * @param ClientCertificate $credentials
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface {
		/* @var User $user */
		$user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials->getEmail()]);
		return $user;
	}

	/**
	 * @param ClientCertificate $credentials
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function checkCredentials($credentials, UserInterface $user): bool {
		return $credentials->isValid();
	}

	public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response {
		if ($request->hasSession()) {
			$request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
		}
		$url = $this->getLoginUrl();
		return new RedirectResponse($url);
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?Response {
		if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
			return new RedirectResponse($targetPath);
		}
		$client = new ClientCertificate();
		$days   = $client->getRemainingDays();
		if ($days > 30) {
			return new RedirectResponse($this->urlGenerator->generate('profile'));
		}
		return new RedirectResponse($this->urlGenerator->generate('user_expire', ['days' => $days]));
	}

	public function supportsRememberMe(): bool {
		return true;
	}

	public function start(Request $request, ?AuthenticationException $authException = null): RedirectResponse {
		$url = $this->getLoginUrl();
		return new RedirectResponse($url);
	}

	protected function getLoginUrl(): string {
		return $this->urlGenerator->generate('user_login');
	}
}

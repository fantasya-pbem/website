<?php
declare(strict_types = 1);
namespace App\Security\X509;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

use App\Entity\User;

class ClientCertificateAuthenticator extends AbstractAuthenticator
{
	use TargetPathTrait;

	private EntityManagerInterface $entityManager;

	public function __construct(ManagerRegistry $managerRegistry, private readonly UrlGeneratorInterface $urlGenerator) {
		$this->entityManager = $managerRegistry->getManager();
	}

	public function supports(Request $request): bool {
		return $request->attributes->get('_route') === 'user_secure' && $request->isMethod('GET');
	}

	public function authenticate(Request $request): Passport {
		$certificate = new ClientCertificate();
		return new Passport(
			new UserBadge($certificate->getEmail(), function ($email) {
				return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
			}),
			new CustomCredentials(function () {
				return true;
			}, null),
			[new RememberMeBadge()]
		);
	}

	public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response {
		if ($request->hasSession()) {
			$request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
		}
		$url = $this->getLoginUrl();
		return new RedirectResponse($url);
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
		if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
			return new RedirectResponse($targetPath);
		}
		$client = new ClientCertificate();
		$days   = $client->getRemainingDays();
		if ($days > 30) {
			return new RedirectResponse($this->urlGenerator->generate('profile'));
		}
		return new RedirectResponse($this->urlGenerator->generate('user_expire', ['days' => $days]));
	}

	private function getLoginUrl(): string {
		return $this->urlGenerator->generate('user_login');
	}
}

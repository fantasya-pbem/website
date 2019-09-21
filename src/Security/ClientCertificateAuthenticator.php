<?php
declare (strict_types = 1);
namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;

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

class ClientCertificateAuthenticator extends AbstractGuardAuthenticator
{
	use TargetPathTrait;

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * @var UrlGeneratorInterface
	 */
	private $urlGenerator;

	/**
 	 * @param EntityManagerInterface $entityManager
	 * @param UrlGeneratorInterface $urlGenerator
	 */
	public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator) {
		$this->entityManager = $entityManager;
		$this->urlGenerator  = $urlGenerator;
	}

	/**
	 * @param Request $request
	 * @return bool
	 */
	public function supports(Request $request): bool {
		return $request->attributes->get('_route') === 'user_secure' && $request->isMethod('GET');
	}

	/**
	 * @param Request $request
	 * @return ClientCertificate
	 */
	public function getCredentials(Request $request): ClientCertificate {
		return new ClientCertificate();
	}

	/**
	 * @param ClientCertificate $credentials
	 * @param UserProviderInterface $userProvider
	 * @return UserInterface|null
	 */
	public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface {
		/* @var User $user */
		$user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials->getEmail()]);
		return $user;
	}

	/**
	 * @param ClientCertificate $credentials
	 * @param UserInterface $user
	 * @return bool
	 */
	public function checkCredentials($credentials, UserInterface $user): bool {
		return $credentials->isValid();
	}

	/**
	 * @param Request $request
	 * @param AuthenticationException $exception
	 * @return Response
	 */
	public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response {
		if ($request->hasSession()) {
			$request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
		}
		$url = $this->getLoginUrl();
		return new RedirectResponse($url);
	}

	/**
	 * @param Request $request
	 * @param TokenInterface $token
	 * @param string $providerKey
	 * @return Response|null
	 */
	public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): Response {
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

	/**
	 * @return bool
	 */
	public function supportsRememberMe(): bool {
		return true;
	}

	/**
	 * @param Request $request
	 * @param AuthenticationException $authException
	 * @return RedirectResponse
	 */
	public function start(Request $request, AuthenticationException $authException = null): RedirectResponse {
		$url = $this->getLoginUrl();
		return new RedirectResponse($url);
	}

	/**
	 * @return string
	 */
	protected function getLoginUrl(): string {
		return $this->urlGenerator->generate('user_login');
	}
}

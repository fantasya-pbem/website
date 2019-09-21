<?php
declare (strict_types = 1);
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class PasswordAuthenticator extends AbstractFormLoginAuthenticator
{
	use TargetPathTrait;

	/**
	 * @var UrlGeneratorInterface
	 */
	private $urlGenerator;

	/**
	 * @var CsrfTokenManagerInterface
	 */
	private $csrfTokenManager;

	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $passwordEncoder;

	/**
	 * @param UrlGeneratorInterface $urlGenerator
	 * @param CsrfTokenManagerInterface $csrfTokenManager
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 */
	public function __construct(UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder) {
		$this->urlGenerator	 = $urlGenerator;
		$this->csrfTokenManager = $csrfTokenManager;
		$this->passwordEncoder  = $passwordEncoder;
	}

	/**
	 * @param Request $request
	 * @return bool
	 */
	public function supports(Request $request): bool {
		return $request->attributes->get('_route') === 'user_login' && $request->isMethod('POST');
	}

	/**
	 * @param Request $request
	 * @return array
	 */
	public function getCredentials(Request $request): array {
		$credentials = [
			'name'	     => $request->request->get('name'),
			'password'   => $request->request->get('password'),
			'csrf_token' => $request->request->get('_csrf_token'),
		];
		$request->getSession()->set(Security::LAST_USERNAME, $credentials['name']);
		return $credentials;
	}

	/**
	 * @param mixed $credentials
	 * @param UserProviderInterface $userProvider
	 * @return UserInterface
	 */
	public function getUser($credentials, UserProviderInterface $userProvider): UserInterface {
		$token = new CsrfToken('authenticate', $credentials['csrf_token']);
		if (!$this->csrfTokenManager->isTokenValid($token)) {
			throw new InvalidCsrfTokenException();
		}
		$user = $userProvider->loadUserByUsername($credentials['name']);
		if (!$user) {
			throw new CustomUserMessageAuthenticationException('Name could not be found.');
		}
		return $user;
	}

	/**
	 * @param mixed $credentials
	 * @param UserInterface $user
	 * @return bool
	 */
	public function checkCredentials($credentials, UserInterface $user): bool {
		return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
	}

	/**
	 * @param Request $request
	 * @param TokenInterface $token
	 * @param string $providerKey
	 * @return RedirectResponse
	 */
	public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse {
		if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
			return new RedirectResponse($targetPath);
		}
		return new RedirectResponse($this->urlGenerator->generate('profile'));
	}

	/**
	 * @return string
	 */
	protected function getLoginUrl(): string {
		return $this->urlGenerator->generate('user_login');
	}
}

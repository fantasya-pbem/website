<?php
declare (strict_types = 1);
namespace App\Security;

use JetBrains\PhpStorm\ArrayShape;
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

	public function __construct(private UrlGeneratorInterface $urlGenerator, private CsrfTokenManagerInterface $csrfTokenManager,
								private UserPasswordEncoderInterface $passwordEncoder) {
	}

	public function supports(Request $request): bool {
		return $request->attributes->get('_route') === 'user_login' && $request->isMethod('POST');
	}

	#[ArrayShape(['name' => 'mixed', 'password' => 'mixed', 'csrf_token' => 'mixed'])]
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
	 * @noinspection PhpMissingParamTypeInspection
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
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function checkCredentials($credentials, UserInterface $user): bool {
		return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): RedirectResponse {
		if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
			return new RedirectResponse($targetPath);
		}
		return new RedirectResponse($this->urlGenerator->generate('profile'));
	}

	protected function getLoginUrl(): string {
		return $this->urlGenerator->generate('user_login');
	}
}

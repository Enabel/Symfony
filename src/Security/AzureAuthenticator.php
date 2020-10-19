<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\AbstractProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use TheNetworg\OAuth2\Client\Provider\Azure;
use TheNetworg\OAuth2\Client\Provider\AzureResourceOwner;
use TheNetworg\OAuth2\Client\Token\AccessToken;

/**
 * Class AzureAuthenticator
 *
 * @author Damien Lagae <damien.lagae@enabel.be>
 */
class AzureAuthenticator extends SocialAuthenticator
{
    use TargetPathTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ClientRegistry
     */
    private $clientRegistry;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(RouterInterface $router, ClientRegistry $clientRegistry, UserRepository $userRepository)
    {
        $this->router = $router;
        $this->clientRegistry = $clientRegistry;
        $this->userRepository = $userRepository;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->router->generate('azure_login'));
    }

    public function supports(Request $request)
    {
        return 'oauth_check' === $request->attributes->get('_route') && $request->get('service') === 'azure';
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getClient());
    }

    /**
     * @param AccessToken           $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return User|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {

        /** @var AzureResourceOwner $azureUser */
        $azureUser = $this->getClient()->fetchUserFromToken($credentials);

        /** @var Azure $provider */
        $provider = $this->getProvider();
        $baseGraphUri = $provider->getRootMicrosoftGraphUri(null);
        $provider->scope = 'your scope ' . $baseGraphUri . '/User.Read';

        // Call a query /me to get the profile
        $extraInfo = $provider->get($provider->getRootMicrosoftGraphUri($credentials) . '/v1.0/me', $credentials);

        return $this->userRepository->findOrCreateFromAzure($azureUser, $extraInfo);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse($this->router->generate('app_login'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);

        return new RedirectResponse($targetPath ?: 'homepage');
    }

    /**
     * @return OAuth2ClientInterface
     */
    public function getClient()
    {
        return $this->clientRegistry->getClient('azure');
    }

    /**
     * @return AbstractProvider|Azure
     */
    public function getProvider()
    {
        return $this->getClient()->getOAuth2Provider();
    }
}

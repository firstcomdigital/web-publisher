<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Security\Authenticator;

use SWP\Bundle\CoreBundle\Model\User;
use SWP\Bundle\CoreBundle\Repository\ApiKeyRepository;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var ApiKeyRepository
     */
    protected $apiKeyRepository;

    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    /**
     * @var TenantRepositoryInterface
     */
    protected $tenantRepository;

    /**
     * TokenAuthenticator constructor.
     *
     * @param ApiKeyRepository          $apiKeyRepository
     * @param TenantContextInterface    $tenantContext
     * @param TenantRepositoryInterface $tenantRepository
     */
    public function __construct(
        ApiKeyRepository $apiKeyRepository,
        TenantContextInterface $tenantContext,
        TenantRepositoryInterface $tenantRepository
    ) {
        $this->apiKeyRepository = $apiKeyRepository;
        $this->tenantContext = $tenantContext;
        $this->tenantRepository = $tenantRepository;
    }

    /**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
     */
    public function getCredentials(Request $request)
    {
        if (!$token = $request->headers->get('Authorization', $request->query->get('auth_token'))) {
            // no token? Return null and no other methods will be called
            return;
        }

        // What you return here will be passed to getUser() as $credentials
        return [
            'token' => $token,
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $apiKey = $this->apiKeyRepository
            ->getValidToken(str_replace('Basic ', '', $credentials['token']))
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $apiKey) {
            return;
        }

        // extend valid time after login
        $apiKey->extendValidTo();
        $this->apiKeyRepository->flush();

        $user = $apiKey->getUser();
        $user->addRole('ROLE_INTERNAL_API');

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if ($user instanceof User) {
            $currentTenantOrganization = $this->tenantContext->getTenant()->getOrganization();
            $userTenantOrganization = $this->tenantRepository->findOneByCode($user->getTenantCode());

            if ($currentTenantOrganization->getId() === $userTenantOrganization->getId()) {
                return true;
            }
        }

        return false;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'status' => 403,
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, 403);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'status' => 401,
            'message' => 'Authentication Required',
        ];

        return new JsonResponse($data, 401);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}

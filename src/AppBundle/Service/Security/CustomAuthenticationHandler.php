<?php
// src/AppBundle/Service/Security/CustomAuthenticationHandler.php
namespace AppBundle\Service\Security;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException,
    Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface,
    Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface,
    Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\Security\Core\Authorization\AuthorizationChecker,
    Symfony\Component\Security\Core\Security,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Bundle\FrameworkBundle\Routing\Router;

class CustomAuthenticationHandler implements
    AuthenticationEntryPointInterface,
    AuthenticationSuccessHandlerInterface,
    AuthenticationFailureHandlerInterface
{
    public $_router;

    public $_authorizationChecker;

    public function __construct(Router $router, AuthorizationChecker $authorizationChecker)
    {
        $this->_router               = $router;
        $this->_authorizationChecker = $authorizationChecker;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        if( $this->_authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY') ) {
            return new RedirectResponse($this->_router->generate('private_office'));
        }

        throw new AccessDeniedHttpException();
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        return new RedirectResponse($this->_router->generate('private_office'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse($request->headers->get('referer'));
    }
}
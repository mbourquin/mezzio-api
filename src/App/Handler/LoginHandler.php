<?php 

namespace App\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse; // add this line
use Laminas\Diactoros\Uri;                       // add this line
use Mezzio\Authentication\Session\PhpSession;    // add this line
use Mezzio\Session\SessionInterface;             // add this line
use Mezzio\Authentication\UserInterface;         // add this line
use Mezzio\Template\TemplateRendererInterface;
use Mezzio\Authentication\AuthenticationInterface;


class LoginHandler implements RequestHandlerInterface
{
    private const REDIRECT_ATTRIBUTE = 'authentication:redirect';

    /** @var AuthenticationInterface */
    private $adapter;

    /** @var TemplateRendererInterface */
    private $renderer;

    public function __construct(TemplateRendererInterface $renderer, AuthenticationInterface $adapter)
    {
        $this->renderer = $renderer;
        $this->adapter = $adapter;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $session  = $request->getAttribute('session');
        $redirect = $this->getRedirect($request, $session);

        // Handle submitted credentials
        if ('POST' === $request->getMethod()) {
            return $this->handleLoginAttempt($request, $session, $redirect);
        }

        // Display initial login form
        $session->set(self::REDIRECT_ATTRIBUTE, $redirect);
        return new HtmlResponse($this->renderer->render(
            'app::login',
            []
        ));
    }

    private function getRedirect(
        ServerRequestInterface $request,
        SessionInterface $session
    ) : string {
        $redirect = $session->get(self::REDIRECT_ATTRIBUTE);

        if (! $redirect) {
            $redirect = new Uri($request->getHeaderLine('Referer'));
            if (in_array($redirect->getPath(), ['', '/oauth2/login'], true)) {
                $redirect = '/';
            }
        }

        return $redirect;
    }

    private function handleLoginAttempt(
        ServerRequestInterface $request,
        SessionInterface $session,
        string $redirect
    ) : ResponseInterface {
        // User session takes precedence over user/pass POST in
        // the auth adapter so we remove the session prior
        // to auth attempt
        $session->unset(UserInterface::class);

        // Login was successful
        if ($this->adapter->authenticate($request)) {
            $session->unset(self::REDIRECT_ATTRIBUTE);
            return new RedirectResponse($redirect);
        }

        // Login failed
        return new HtmlResponse($this->renderer->render(
            'app::login',
            ['error' => 'Invalid credentials; please try again']
        ));
    }
}
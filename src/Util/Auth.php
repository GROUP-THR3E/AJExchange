<?php

namespace GroupThr3e\AJExchange\Util;

use GroupThr3e\AJExchange\Models\User;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class Auth
{
    private ?User $user = null;
    private static ?Auth $auth = null;
    public static string $AUTH_URL = '/users/login';

    /**
     * Authenticates the user with the request cookie
     * @param RequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function authenticateRequest(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Checks if the session is set and looks up user
        if (isset($_SESSION['user'])) {
            $userDataset = new UserDataset();
            $user = $userDataset->getUser($_SESSION['user']);
            $this->user = $user;
        }

        return $handler->handle($request);
    }

    public function authorizeRequest(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        if (($path === self::$AUTH_URL) && $this->user !== null)
            return (new Response())->withHeader('Location', '/')->withStatus(302);
        else if ($path !== self::$AUTH_URL && $this-> user === null)
            return (new Response())->withHeader('Location', self::$AUTH_URL)->withStatus(302);

        return $handler->handle($request);
    }

    /**
     * Authenticates the given user
     * @param string $email
     * @param string $password
     * @return User|string
     */
    public function authenticateCredentials(string $email, string $password): User|string
    {
        $userDataset = new UserDataset();
        $user = $userDataset->getUserByEmail($email);
        if ($user === null) return 'No user with give email';

        $hashCheck = password_verify($password, $user->getPassword());
        if (!$hashCheck) return 'Incorrect password';

        $this->user = $user;
        $_SESSION['user'] = $user->getUserId();
        return $user;
    }

    /**
     * Logs the user out
     * @return void
     */
    public function logout(): void
    {
        $this->user = null;
        unset($_SESSION['user']);
    }

    /**
     * @return User|null the current user, null if the user isn't authenticated
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return Auth returns the current auth instance
     */
    public static function getAuthManager(): Auth
    {
        if (self::$auth === null) self::$auth = new Auth();
        return self::$auth;
    }
}
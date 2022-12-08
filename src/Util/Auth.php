<?php

namespace GroupThr3e\AJExchange\Util;

use GroupThr3e\AJExchange\Models\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use PSR7Sessions\Storageless\Http\SessionMiddleware;

class Auth
{
    private ?User $user = null;
    private static ?Auth $auth = null;

    /**
     * Authenticates the user with the request cookie
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     */
    public function authenticateRequest(Request $request, RequestHandler $handler): Response
    {
        // Checks if the session is set and looks up user
        if ($_SESSION['user'] !== null) {
            $userDataset = new UserDataset();
            $user = $userDataset->getUser($_SESSION['user']);
            $this->user = $user;
        }

        return $handler->handle($request);
    }

    /**
     * Authenticates the given user
     * @param string $email
     * @param string $password
     * @param Request $request
     * @return User|string
     */
    public function authenticateCredentials(string $email, string $password, Request $request): User|string
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
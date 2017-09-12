<?php
namespace FiremonPHP\Auth;


use FiremonPHP\Auth\Entity\User;
use FiremonPHP\Auth\Entity\UserMapper;

class Authentication
{
    /**
     * @var UserMapper
     */
    private $userMapper;

    /**
     * @var User
     */
    private $userLogged;

    public function __construct()
    {
        $this->userMapper = new UserMapper();
    }

    /**
     * @param string $username
     * @param $password
     * @return User
     */
    public function login(string $username, $password)
    {
        $user = $this->userMapper->login($username, $password);
        return $user;
    }

    public function loginWithToken(string $token)
    {
        return $this->userMapper->loginWithToken($token);
    }

    /**
     * @param User $user
     * @return User
     */
    public function edit(User $user)
    {
        return $this->userMapper->edit($user);
    }

    /**
     * @param string $username
     * @return bool|string
     */
    public function rememberPassword(string $username)
    {
        return $this->userMapper->rememberPassword($username);
    }

    /**
     * @param string $username
     * @param string $rememberToken
     * @return bool
     */
    public function changePasswordByToken(string $username, string $rememberToken) : bool
    {
        return $this->userMapper->changePassword($username, $rememberToken);
    }

    /**
     * @param string $username
     * @param $password
     * @param array $personalData
     * @return User
     */
    public function register(string $username, $password, array $personalData = []) : User
    {
        $user = $this->userMapper->register($username, $password, $personalData);
        return $user;
    }

    /**
     * @param string $token
     * @return bool
     */
    public function validateJwt(string $token) : bool
    {
        return $this->userMapper->validateJwt($token);
    }

    /**
     * @param User $user
     */
    public function setLogged(User $user)
    {
        $this->userLogged = $user;
    }
}
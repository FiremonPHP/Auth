<?php
namespace FiremonPHP\Auth\Entity;


use Firebase\JWT\JWT;
use FiremonPHP\Auth\Configuration;
use FiremonPHP\Auth\PasswordHash\PasswordHash;
use FiremonPHP\Manager\Exceptions\MongoDBExceptions;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Driver\Exception\BulkWriteException;

class UserMapper
{
    /**
     * @var \FiremonPHP\Manager\Manager
     */
    private $_manager;
    /**
     * @var array
     */
    protected $configuredFields = [];
    /**
     * @var string
     */
    protected $collectionName;
    /**
     * @var string
     */
    protected $securitySalt;

    public function __construct()
    {
        $this->_manager = Configuration::getManger();
        $this->configuredFields = Configuration::getFields();
        $this->collectionName = Configuration::getCollectionName();
        $this->securitySalt = Configuration::getSecuritySalt();
    }

    /**
     * @param User $user
     * @return User
     */
    public function edit(User $user)
    {
        $user
            ->setModified(new UTCDateTime());

        $this->_manager
            ->update($this->collectionName, $this->mapperToDatabase($user->toArray()))
            ->equalTo($this->configuredFields['username'], $user->getUsername())
            ->persist();
        try {
            $this->_manager->execute();
        } catch (BulkWriteException $exception)
        {
            $errors = MongoDBExceptions::get($exception->getWriteResult());
            $user->setErrors($errors);
        }
        return $user;
    }

    /**
     * @param string $username
     * @param $password
     * @return User
     */
    public function login(string $username, $password)
    {
        $user = new User();
        $user
            ->setUsername($username)
            ->setPassword($password);
        $userData = $this->findUser(
            $this->configuredFields['username'],
            $user->getUsername()
        );

        if (!$userData) {
            return $user;
        }

        $hashPassword = new PasswordHash($user->getPassword());
        $logged = $hashPassword->check($user->getPassword(), $userData[$this->configuredFields['password']]);

        if ($logged) {
            $user
                ->setCredentials($userData)
                ->setLogged($logged)
                ->setExpire(time()+Configuration::getExpireTime());

            $token = $this->setToken($user);

            $user
                ->setToken($token);

            $user = $this->edit($user);
        }
        return $user;
    }

    /**
     * @param string $token
     * @return bool|User
     */
    public function loginWithToken(string $token)
    {
        $decodedToken = (array)JWT::decode($token, $this->securitySalt, ['HS256']);
        $userData = $this->_manager
            ->find($this->collectionName)
            ->equalTo($this->configuredFields['username'], $decodedToken['username'])
            ->equalTo($this->configuredFields['token'], $token)
            ->execute();

        $userData = $userData->toArray();

        if (!isset($userData[0])) {
            return false;
        }

        $user = new User($userData[0]);

        if (!$user->isLogged()) {
            return false;
        }

        return $user;
    }

    /**
     * @param string $username
     * @param $password
     * @param array $personalData
     * @return User
     */
    public function register(string $username, $password, array $personalData = [])
    {
        $user = new User(
            $this->mapperToEntity($personalData)
        );

        $user
            ->setUsername($username)
            ->setCreated(new UTCDateTime());
        $user
            ->setPassword(new PasswordHash($password));

        $this->_manager->insert(
            $this->collectionName,
            $this->mapperToDatabase($user->toArray())
        );

        try {
            $this->_manager->execute();
        } catch (BulkWriteException $exception) {
            $errors = MongoDBExceptions::get($exception->getWriteResult());
            $user->setErrors($errors);
            $user->setActive(false);
        }

        return $user;
    }

    /**
     * @param string $username
     * @return bool|string
     */
    public function rememberPassword(string $username)
    {
        $userData = $this->findUser($this->configuredFields['username'], $username);
        if ($userData) {
            $rememberToken = $this->getTokenToRemember();
            $user = new User(
                $this->mapperToEntity($userData)
            );
            $user
                ->setRememberToken($rememberToken);
            $this->edit($user);
            return $rememberToken;
        }
        return false;
    }

    /**
     * @param string $username
     * @param string $rememberToken
     * @return bool
     */
    public function changePassword(string $username, string $rememberToken)
    {
        $userData = $this->findUser($this->configuredFields['username'], $username);
        if (!$userData) {
            return false;
        }

        $user = new User($this->mapperToEntity($userData));

        if ($this->rememberTokenIsValid($rememberToken, $user)) {
            return true;
        }
    }

    /**
     * @param string $jwToken
     * @return bool
     */
    public function validateJwt(string $jwToken)
    {
        try {
            JWT::decode($jwToken, $this->securitySalt, ['HS256']);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Find user by condition
     * @param $condition
     * @param $value
     * @return bool|array
     */
    private function findUser($condition, $value)
    {
        $result = $this->_manager
            ->find($this->collectionName)
            ->limit(1)
            ->equalTo($condition, $value)
            ->execute();

        $result = $result->toArray();

        if (isset($result[0])) {
            return $result[0];
        }
        return false;
    }

    /**
     * @param array $data
     * @return array
     */
    private function mapperToDatabase(array $data)
    {
        $mappedData = [];
        foreach ($this->configuredFields as $key => $value) {
            $mappedData[$value] = $data[$key];
        }
        return $mappedData;
    }

    /**
     * @param array $data
     * @return array
     */
    private function mapperToEntity(array $data)
    {
        $mappedData = [];
        foreach ($data as $key => $value) {
            if (isset($this->configuredFields[$key])) {
                $mappedData[$key] = $value;
                continue;
            }
            $mappedData['personal'][$key] = $value;
        }
        return $mappedData;
    }

    /**
     * @return string
     */
    private function getTokenToRemember()
    {
        return md5(uniqid(rand(), true));
    }

    /**
     * Compare two tokens, token on bd and date of last modified.
     * if modified have diff above configurated hour, return true
     * @param string $requestToken
     * @param User $user
     * @return bool
     */
    private function rememberTokenIsValid(string $requestToken, User $user)
    {
        $token = $user->getRememberToken();
        $lastModified = $user
            ->getModified()
            ->toDateTime();

        if ($token !== $requestToken) {
            return false;
        }

        $diff = $lastModified->diff((new \DateTime('now')));

        return $diff->h < (Configuration::getRememberTime() / 60 / 60);

    }

    /**
     * @param User $user
     * @return string
     */
    private function setToken(User $user)
    {
        $payload = $this->setConfiguratedTokenFields($user);
        $payload['sub'] = time()+Configuration::getExpireTime();
        $payload['uri'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return JWT::encode($payload, $this->securitySalt);
    }

    /**
     * @param User $user
     * @return array
     */
    private function setConfiguratedTokenFields(User $user)
    {
        $configuredTokenFields = [];
        foreach (Configuration::getTokenFields() as $field) {
            $properValue = $this->getValueOnEntity($user, $field);
            if ($properValue !== false) {
                $configuredTokenFields[$field] = $properValue;
            }
        }
        return $configuredTokenFields;
    }

    /**
     * @param User $entity
     * @param $value
     * @return null
     */
    private function getValueOnEntity(User $entity, $value)
    {
        if (method_exists($entity, 'get'.$value)) {
            return $entity->{'get'.$value}();
        }

        print_r($entity);

        if ($entity->getPersonal()->{$value}) {
            return $entity->getPersonal()->{$value};
        }
        return false;
    }
}
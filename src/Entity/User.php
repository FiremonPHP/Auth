<?php
namespace FiremonPHP\Auth\Entity;

use FiremonPHP\Auth\Configuration;

class User
{
    /**
     * @var array
     */
    private $errors = [];
    /**
     * @var \MongoDB\BSON\ObjectID
     */
    protected $_id;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $username;
    /**
     * @var string
     */
    protected $email;
    /**
     * Hashed password
     * @var string
     */
    protected $password;
    /**
     * @var bool
     */
    protected $logged = false;
    /**
     * @var bool
     */
    protected $active = true;
    /**
     * @var array
     */
    protected $roles = [];
    /**
     * @var \MongoDB\BSON\UTCDateTime
     */
    protected $modified;
    /**
     * @var \MongoDB\BSON\UTCDateTime
     */
    protected $created;
    /**
     * @var Personal
     */
    protected $personal;

    protected $token;

    protected $expire;

    protected $remember_token;

    public function __construct(array $credentials = [])
    {
        $this->personal = new Personal();
        $this->setCredentials($credentials);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return User
     */
    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return \MongoDB\BSON\ObjectID
     */
    public function getId(): \MongoDB\BSON\ObjectID
    {
        return $this->_id;
    }

    /**
     * @param \MongoDB\BSON\ObjectID $id
     * @return User
     */
    public function set_Id(\MongoDB\BSON\ObjectID $id): User
    {
        $this->_id = $id;
        return $this;
    }



    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     * @return User
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * @param mixed $remember_token
     * @return User
     */
    public function setRememberToken($remember_token)
    {
        $this->remember_token = $remember_token;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getExpire()
    {
        return $this->expire;
    }

    /**
     * @param mixed $expire
     * @return User
     */
    public function setExpire($expire)
    {
        $this->expire = $expire;
        return $this;
    }



    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return \MongoDB\BSON\UTCDateTime
     */
    public function getModified(): \MongoDB\BSON\UTCDateTime
    {
        return $this->modified;
    }

    /**
     * @param \MongoDB\BSON\UTCDateTime $modified
     * @return User
     */
    public function setModified(\MongoDB\BSON\UTCDateTime $modified): User
    {
        $this->modified = $modified;
        return $this;
    }

    /**
     * @return \MongoDB\BSON\UTCDateTime
     */
    public function getCreated(): \MongoDB\BSON\UTCDateTime
    {
        return $this->created;
    }

    /**
     * @param \MongoDB\BSON\UTCDateTime $created
     * @return User
     */
    public function setCreated(\MongoDB\BSON\UTCDateTime $created): User
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return bool
     */
    public function isLogged(): bool
    {
        return $this->logged;
    }

    /**
     * @param bool $logged
     * @return User
     */
    public function setLogged(bool $logged): User
    {
        $this->logged = $logged;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return User
     */
    public function setActive(bool $active): User
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     * @return User
     */
    public function setRoles($roles): User
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return Personal
     */
    public function getPersonal()
    {
        return $this->personal;
    }

    /**
     * Set personal information by array structure
     * If any configured data found on array, set it on propertie
     * @param $data
     * @return $this
     */
    public function setPersonal(array $data)
    {
        foreach ($data as $key => $value) {
            $this->personal->{$key} = $value;
        }
        return $this;
    }

    /**
     * @param array $credentials
     * @return $this
     */
    public function setCredentials(array $credentials)
    {
        foreach ($credentials as $key => $value) {
            if ($value !== null) {
                $this->{'set'.$key}($value);
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'password' => $this->password,
            'token' => $this->token,
            'expire' => $this->expire,
            'logged' => $this->logged,
            'created' => $this->created,
            'modified' => $this->modified,
            'active' => $this->active,
            'roles' => $this->roles,
            'personal' => (array)$this->personal
        ];
    }

    /**
     * @param array $errors
     * @return $this
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @return array
     */
    public function getErros()
    {
        return $this->errors;
    }

}
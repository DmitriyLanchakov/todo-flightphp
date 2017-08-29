<?php

namespace Model\User;

use Model\Todo\Todo;
use SimpleDb\Data\Model;
use SimpleDb\Data\Field;
use SimpleDb\Relations\Many;

class User extends Model
{
    protected $id;
    protected $email;
    protected $password;

    protected function table()
    {
        return 'user';
    }

    protected function fields()
    {
        return [
            'id'       => Field::INT,
            'email'    => Field::STRING,
            'password' => Field::STRING,
        ];
    }

    protected function relations() {
        return [
            'todos' => new Many(Todo::class, 'user_id', 'id'),
        ];
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return mixed
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }


}
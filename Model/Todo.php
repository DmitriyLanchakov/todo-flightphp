<?php

namespace Model\Todo;

use Model\User\User;
use SimpleDb\Data\Model;
use SimpleDb\Data\Field;
use SimpleDb\Relations\One;

class Todo extends Model
{
    protected $id;
    protected $title;
    protected $user_id;
    protected $is_completed;

    protected function table() {
        return 'todo';
    }

    protected function fields() {
        return [
            'id'           => Field::INT,
            'user_id'      => Field::INT,
            'is_completed' => Field::INT,
            'title'        => Field::STRING,
        ];
    }

    protected function relations() {
        return [
            'user' => new One(User::class, 'user_id', 'id'),
        ];
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     * @return Todo
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Todo
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return Todo
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCompleted()
    {
        return $this->is_completed;
    }

    /**
     * @param bool $is_completed
     * @return Todo
     */
    public function setIsCompleted($is_completed)
    {
        $this->is_completed = $is_completed;
        return $this;
    }


}
<?php

require_once 'vendor/autoload.php';
require_once 'database_config.php';

use SimpleDb\Database;
use Util\Util\Util;
use Model\User\User;
use Model\Todo\Todo;

$db = new Database(DATABASE_LOGIN . ':' . DATABASE_PASSWORD . '@' . DATABASE_HOST . '/' . DATABASE_NAME);

Flight::route('POST /signup.php', function () use ($db) {
    $email     = Flight::request()->data->email;
    $password  = Flight::request()->data->password;
    $password2 = Flight::request()->data->password2;
    $hash      = Util::hash($email, $password);

    if (!Util::validEmail($email)) {
        Flight::json(['desc' => 'Email is not valid', 'status' => 500], 500);
        return;
    }

    if (!Util::validPasswords($password, $password2)) {
        Flight::json(['desc' => 'Passwords are not equal', 'status' => 500], 500);
    }

    $user = User::from($db->table('user')->oneWhere(['email' => $email]));

    if ($user) {
        Flight::json(['status' => 500, 'desc' => 'Email aleready exists'], 500);
    } else {
        $user = new User(['email' => $email, 'password' => $hash]);
        $user->save();

        Flight::json(['hash'   => $hash, 'status' => 201], 201);
    }
});

Flight::route('POST /signin.php', function () use ($db) {
    $email    = Flight::request()->data->email;
    $password = Flight::request()->data->password;
    $hash     = Util::hash($email, $password);
    $user     = User::from($db->table('user')->oneWhere(['email' => $email, 'password' => $hash]));

    if ($user) {
        Flight::json(['hash' => $hash, 'status' => 200], 200);
    } else {
        Flight::json(['desc' => 'There is no user', 'status' => 400], 400);
    }
});

Flight::route('POST /create_todo.php', function () use ($db) {
    $hash         = Flight::request()->data->hash;
    $title        = Flight::request()->data->title;
    $is_completed = Flight::request()->data->is_completed;
    $user         = User::from($db->table('user')->oneWhere(['password' => $hash]));

    if ($user) {
        $todo = new Todo(['title' => $title, 'is_completed' => $is_completed, 'user_id' => $user->id]);
        $todo->save();

        Flight::json(['status' => 200, 'id' => $db->lastInsertId], 200);
    } else {
        Flight::json(['desc' => 'There is no user', 'status' => 401], 401);
    }
});

Flight::route('POST /set_completed_todo.php', function () use ($db) {
    $hash         = Flight::request()->data->hash;
    $id           = Flight::request()->data->id;
    $is_completed = Flight::request()->data->is_completed;

    $user = User::from($db->table('user')->oneWhere(['password' => $hash]));

    if ($user) {
        $result = $db->query('UPDATE todo SET is_completed = ? WHERE id = ? AND user_id = ?', [
            $is_completed,
            $id,
            $user->id,
        ]);

        if ($result) {
            Flight::json(['status' => 200], 200);
        } else {
            Flight::json(['desc' => 'Not found', 'status' => 401], 401);
        }
    } else {
        Flight::json(['desc' => 'Not found', 'status' => 401], 401);
    }
});

Flight::route('POST /set_all_completed_todo.php', function () use ($db) {
    $hash         = Flight::request()->data->hash;
    $is_completed = Flight::request()->data->is_completed;
    $user         = User::from($db->table('user')->oneWhere(['password' => $hash]));

    if ($user) {
        $result = $db->query('UPDATE todo SET is_completed = ? WHERE user_id = ?', [
            $is_completed,
            $user->id,
        ]);

        if ($result) {
            Flight::json(['status' => 200], 200);
        } else {
            Flight::json(['desc' => 'Not found', 'status' => 401], 401);
        }

    } else {
        Flight::json(['desc' => 'Not found', 'status' => 401], 401);
    }
});

Flight::route('POST /update_title_todo.php', function () use ($db) {
    $hash  = Flight::request()->data->hash;
    $id    = Flight::request()->data->id;
    $title = Flight::request()->data->title;
    $user  = User::from($db->table('user')->oneWhere(['password' => $hash]));

    if ($user) {
        $result = $db->query('UPDATE todo SET title = ? WHERE id = ? AND user_id = ?', [$title, $id, $user->id]);

        if ($result) {
            Flight::json(['status' => 200], 200);
        } else {
            Flight::json(['desc' => 'Not found', 'status' => 401], 401);
        }

    } else {
        Flight::json(['desc' => 'Not found', 'status' => 401], 401);
    }
});

Flight::route('POST /delete_todo.php', function () use ($db) {
    $hash  = Flight::request()->data->hash;
    $id    = Flight::request()->data->id;
    $user  = User::from($db->table('user')->oneWhere(['password' => $hash]));

    if ($user) {
        $result = $db->query('DELETE FROM todo WHERE id = ? AND user_id = ?', [$id, $user->id]);

        if ($result) {
            Flight::json(['status' => 200], 200);
        } else {
            Flight::json(['desc' => 'Not found', 'status' => 401], 401);
        }

    } else {
        Flight::json(['desc' => 'Not found', 'status' => 401], 401);
    }
});

Flight::route('POST /delete_completed_todos.php', function () use ($db) {
    $hash = Flight::request()->data->hash;
    $user = User::from($db->table('user')->oneWhere(['password' => $hash]));

    if ($user) {
        $result = $db->query('DELETE FROM todo WHERE is_completed = ? AND user_id = ?', [true, $user->id]);

        if ($result) {
            Flight::json(['status' => 200], 200);
        } else {
            Flight::json(['desc' => 'Not found', 'status' => 401], 401);
        }
    } else {
        Flight::json(['desc' => 'Not found', 'status' => 401], 401);
    }
});

Flight::route('POST /todos.php', function () use ($db) {
    $hash = Flight::request()->data->hash;
    $user = User::from($db->table('user')->oneWhere(['password' => $hash]));

    if ($user) {
        $todos = $db->all('SELECT * FROM todo WHERE user_id = ? ORDER BY id DESC', [$user->id]);

        if ($todos) {
            Flight::json(['status' => 200, 'todos' => $todos], 200);
        } else {
            Flight::json(['desc' => 'Not found', 'status' => 401], 401);
        }
    } else {
        Flight::json(['desc' => 'Not found', 'status' => 401], 401);
    }
});

Flight::start();


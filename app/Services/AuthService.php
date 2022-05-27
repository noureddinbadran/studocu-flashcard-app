<?php

namespace App\Services;

use App\Exceptions\UserException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService extends BaseService
{
    public function __construct(User $user) {
        $this->model = $user;
    }

    public function register(array $data) : void
    {
        $this->model->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['email']),
        ]);
    }

    public function login(array $data)
    {
        $user = $this->model->whereEmail($data['email'])->first();

//        if (!$user || !Hash::check($data['password'], $user->password))
//            throw new UserException('invalid_credentials ', 'invalid_credentials', 401);

        return $user;
    }
}
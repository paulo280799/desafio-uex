<?php

namespace App\Repositories;

use App\Models\User;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class UserRepository
{
    public function __construct(
        protected User $user
    ) {
    }

    public function create(array $data): User
    {
        $data['password'] = bcrypt($data['password']);
        return $this->user->create($data);
    }

    public function delete(int $id): Void
    {
        $this->user->where('id', $id)->delete();
    }

    public function getUserByEmail($email): User
    {
        return $this->user->where('email', $email)->firstOrFail();
    }

    public function resetPassword(array $data): string
    {
        return Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

            }
        );
    }
}

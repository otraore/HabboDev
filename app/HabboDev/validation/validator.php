<?php

namespace HabboDev\Validation;

use Violin\Violin;
use HabboDev\User\User;
use HabboDev\Helpers\Hash;

class Validator extends Violin
{
    protected $user;

    protected $hash;

    protected $auth;

    public function __construct(User $user, Hash $hash, $auth = null)
    {
        $this->user = $user;
        $this->hash = $hash;
        $this->auth = $auth;

        $this->addFieldMessages([
           'email' => [
               'required' => 'Email cannot be empty',
               'email' => 'Not a valid email',
               'uniqueEmail' => 'That email is already in use.'
           ],
            'username' => [
                'required' => 'Username cannot be empty',
                'uniqueUsername' => 'That username is already in use.',
                'max' => 'Username can\'t be greater than {$0}'
            ],
            'password' => [
              'required' => 'Password cannot be empty',
                'min' => 'Password must be a minimum of {$0}'
            ],
            'password_confirm' => [
                'required' => 'Cannot be blank',
                'matches' => "Passwords must match"
            ]
        ]);

        $this->addRuleMessages([
           'matchesCurrentPassword' => 'That does not match your current password',
            'differentPassword' => "Your new password can't be the same as your old password"
        ]);
    }

    public function validate_uniqueEmail($value, $input, $args)
    {
        return ! (bool) $this->user->where('email', $value)->count();
    }

    public function validate_uniqueUsername($value, $input, $args)
    {
        return ! (bool) $this->user->where('username', $value)->count();
    }

    public function validate_matchesCurrentPassword($value, $input, $args)
    {
        if($this->auth && $this->hash->passwordCheck($value, $this->auth->password)){
            return true;
        } return false;
    }

    public function validate_differentPassword($value, $input, $args)
    {
        if($this->hash->passwordCheck($value, $this->auth->password)){
            return false;
        } return true;
    }
}
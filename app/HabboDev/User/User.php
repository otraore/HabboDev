<?php

namespace HabboDev\User;

use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent
{
    protected $table = 'users';

    protected  $fillable = [
       'email',
        'username',
        'password',
        'habbo_username',
        'active',
        'active_hash',
        'remember_identifier',
        'remember_token',

    ] ;

    public function getFullName()
    {
        if(!$this->first_name || !$this->last_name){
            return null;
        } return "{$this->first_name} {$this->last_name}";
    }

    public function getFullnameOrUsername()
    {
        return $this->getFullName() ? : $this->username;
    }

    public function activateAccount()
    {
        $this->update([
            'active' => true,
            'active_hash' => NULL
        ]);
    }

    public function getAvatarUrl($options = [])
    {
        return $this->getGravatarUrl($options);
    }

    public function getGravatarUrl($options = [])
    {
        $size = isset($options['size']) ? $options['size'] : 45;
        $email = md5(strtolower($this->email));
        return 'http://www.gravatar.com/avatar/'. $email . '?s=' .$size . '&d=mm';
    }

    public function updateRememberCredentials($identifier, $token){
        $this->update([
            'remember_identifier' => $identifier,
            'remember_token' => $token
        ]);
    }

    public function removeRememberCredentials()
    {
        $this->updateRememberCredentials(null, null);
    }

    public function isAdmin()
    {
        return $this->hasPermission('is_admin');
    }

    public function hasPermission($permission)
    {
        return (bool) $this->permissions->{$permission};
    }

    public function permissions()
    {
        return $this->hasOne('HabboDev\User\UserPermission', 'user_id');
    }
}


<?php

namespace HabboDev\Project;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Project extends Eloquent
{
    protected $table = 'projects';

    protected $fillable = [
        'user_id',
        'project_name',
        'type'
    ];

}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Role extends Model
{
    const CREATED_AT      = 'time_created';
	const UPDATED_AT      = 'time_updated';
	
	protected $table      = 'role';
	protected $primaryKey = 'id';
	protected function serializeDate(DateTimeInterface $date)
	{
	    return $date->format('Y-m-d H:i:s');
	}
	protected $fillable   = [
	  	'role'
      	,'description'
      	,'note'
      	,'user_created'
      	,'time_created'
      	,'user_updated'
      	,'time_updated'
      	,'isdelete'
	];

	public function user_created()
	{
	    return $this->hasOne('App\Models\User', 'id', 'user_created')->whereIsdelete(0);
	}

	public function user_updated()
	{
	    return $this->hasOne('App\Models\User', 'id', 'user_updated')->whereIsdelete(0);
	}

	

}
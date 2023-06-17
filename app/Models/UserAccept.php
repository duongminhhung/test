<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class UserAccept extends Model
{
    const CREATED_AT      = 'Time_created';
	const UPDATED_AT      = 'Time_updated';
	
	protected $table      = 'User_Accept';
	protected $primaryKey = 'ID';
	protected function serializeDate(DateTimeInterface $date)
	{
	    return $date->format('Y-m-d H:i:s');
	}
	protected $fillable   = [
	  	'User_ID'
      	,'Command_ID'
      	,'Type'
		,'Status'
      	,'Time_created'
      	,'Time_updated'
      	,'Isdelete'
	];

	public function user()
	{
	    return $this->hasOne('App\Models\User', 'id', 'User_ID')->whereIsdelete(0);
	}


	

}

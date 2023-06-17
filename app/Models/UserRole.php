<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class UserRole extends Model
{
	// const CREATED_AT      = 'time_created';
	// const UPDATED_AT      = 'time_updated';

	protected $table      = 'user_role';
	protected $primaryKey = 'id';
	// protected function serializeDate(DateTimeInterface $date)
	// {
	// 	return $date->format('Y-m-d H:i:s');
	// }
	protected $fillable   = [
		'user_id', 'role_id'
	];

	// public function user_created()
	// {
	//     return $this->hasOne('App\Models\User', 'id', 'user_created')->whereIsdelete(0);
	// }

	// public function user_updated()
	// {
	//     return $this->hasOne('App\Models\User', 'id', 'user_updated')->whereIsdelete(0);
	// }
}

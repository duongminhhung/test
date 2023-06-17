<?php

namespace App\Models;

trait Basic
{
	// private $userCreate = 'User_Updated';
	public function user_created()
	{
		return $this->hasOne('App\Models\User', 'id', 'User_Created')->whereIsdelete(0);
	}

	public function user_updated()
	{
		return $this->hasOne('App\Models\User', 'id', 'User_Updated')->whereIsdelete(0);
	}
}

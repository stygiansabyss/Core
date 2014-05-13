<?php namespace NukaCode\Core;

class User_Preference_UserObserver {

	public function saving($model)
	{
		$model->validateValue();
	}
}
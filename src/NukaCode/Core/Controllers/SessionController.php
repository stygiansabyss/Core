<?php namespace NukaCode\Core\Controllers;

class SessionController extends \BaseController {

    public function postLogin()
    {
        $input = e_array(\Input::all());

        if ($input != null) {
            $userdata = array(
                'username'      => $input['username'],
                'password'      => $input['password']
            );

            if (\Auth::attempt($userdata)) {
                return \Redirect::intended('/');
            }
            else {
                $this->redirect('/login', \Session::get('login_errors'));
            }
        }
    }

    public function postRegister()
    {
        $input = e_array(Input::all());

        if ($input != null) {
            $user            = new User;
            $user->username  = $input['username'];
            $user->password  = $input['password'];
            $user->email     = $input['email'];
            $user->status_id = 1;

            $this->checkErrorsSave($user);

            // Assign the guest role
            $user->roles()->attach(BaseModel::ROLE_GUEST);

            // Give them an inbox
            $inbox          = new \Message_Folder;
            $inbox->user_id = $user->id;
            $inbox->name    = 'Inbox';

            $this->save($inbox);
        }

        return $this->redirect('/');
    }

} 
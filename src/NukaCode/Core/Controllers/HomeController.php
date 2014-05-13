<?php namespace NukaCode\Core\Controllers;

class HomeController extends \BaseController {

    public function getCollapse($target)
    {
        $this->skipView();

        $sessionName = 'COLLAPSE_'. $target;
        if (\Session::get($sessionName)) {
            \Session::put($sessionName, false);

            // Update the user preference
            $preference = $this->activeUser->getPreferenceByKeyName($sessionName);
            $this->activeUser->setPreferenceValue($preference->id, false);
        } else {
            \Session::put($sessionName, true);

            // Update the user preference
            $preference = $this->activeUser->getPreferenceByKeyName($sessionName);
            $this->activeUser->setPreferenceValue($preference->id, true);
        }
    }

    public function getComposerVersion()
    {
        $this->skipView();

        $composer = \File::get(base_path() . '/vendor/composer/installed.json');

        return $composer;

    }
}
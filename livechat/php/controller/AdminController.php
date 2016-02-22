<?php

class AdminController extends Controller
{
    const SOUNDS_DIR        = 'audio';
    const WIDGET_THEMES_DIR = 'widget-themes';

    // Show admin/operator's main page

    public function indexAction()
    {
        $user        = $this->get('user');
        $appSettings = $this->get('config')->data['appSettings'];

        if($user->hasRole('ADMIN'))
        {
            $userData = $user->getData();
        }
        else
        {
            $userEntry = UserModel::repo()->find($user->getId());
            $userData  = $userEntry->getData();
        }

        return $this->render('admin/index.html', array(

            'userData'         => json_encode($userData),
            'installed'        => $appSettings['installed'],
            'installStatus'    => json_encode($this->getInstallStatus()),
            'messageSound'     => $appSettings['messageSound'],
            'defaultAvatars'   => json_encode($this->getDefaultAvatars()),
            'notificationIcon' => $appSettings['notificationIcon'],
            'messageSounds'    => $this->getMessageSounds(),
            'widgetThemes'     => $this->getWidgetThemes()
        ));
    }

    // Get the templates for SPA

    public function templatesAction()
    {
        return $this->render('admin-templates.html');
    }

    // Show the chatting widget

    public function widgetTestAction()
    {
        return $this->render('admin/widget-test.html');
    }

    // Display logs

    public function logsAction()
    {
        $logs = $this->get('logger')->getLogs();

        return $this->text($logs);
    }

    // Clear logs

    public function clearLogsAction()
    {
        $this->get('logger')->clear();

        return $this->json(array('success' => true));
    }

    // Get default profile images

    protected function getDefaultAvatars()
    {
        $avatars  = UserModel::getDefaultAvatars();
        $template = $this->get('template_interface');

        // Make all the names absolute asset paths

        $i = 0;

        foreach($avatars as $a)
        {
            $avatars[$i++] = $template->asset($a);
        }

        return $avatars;
    }

    // Get default new message sounds

    protected function getMessageSounds()
    {
        $sounds = array();

        // Read all sounds from the default sounds directory

        foreach(glob(ROOT_DIR . '/../' . self::SOUNDS_DIR . '/*') as $path)
        {
            $sounds[basename($path, '.mp3')] = self::SOUNDS_DIR . '/' . basename($path);
        }

        return $sounds;
    }

    // Get available widget themes

    protected function getWidgetThemes()
    {
        $themes = array();

        // Read all sounds from the default sounds directory

        foreach(glob(ROOT_DIR . '/../' . self::WIDGET_THEMES_DIR . '/*') as $path)
        {
            $themes[ucfirst(basename($path))] = self::WIDGET_THEMES_DIR . '/' . basename($path);
        }

        return $themes;
    }

    // Installation validity check

    protected function getInstallStatus()
    {
        $validators = $this->get('model_validation');

        // Check if the configuration settings are correct

        $validConfig = count($validators->validateConfig()) === 0;

        // Check if the database structure is correct

        $validDb = count($validators->validateDb()) === 0;

        return compact('validConfig', 'validDb');
    }
}

?>

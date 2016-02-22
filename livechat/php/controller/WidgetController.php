<?php

class WidgetController extends Controller
{
    public function initAction()
    {
        $config = $this->get('config');
        
        return $this->render('widget/init.js.php', array(
        
            'ui'             => $config->data['appSettings'],
            'uiJson'         => str_replace("'", '&apos;', json_encode($config->data['appSettings'])),
            'defaultAvatars' => json_encode($this->getDefaultAvatars())
        
        ), 'js');
    }
    
    public function iframeContentAction()
    {
        $config = $this->get('config');
        
        return $this->render('widget/iframe-content.html.php', array(
        
            'ui'             => $config->data['appSettings'],
            'uiJson'         => str_replace("'", '&apos;', json_encode($config->data['appSettings'])),
            'defaultAvatars' => json_encode($this->getDefaultAvatars()),
            'info'           => str_replace("'", '&apos;', json_encode($this->get('request')->getUserInfo())),
            'remoteDomain'   => $this->get('request')->getVar('domain')
        ));
    }
    
    public function mobileViewAction()
    {
        $config              = $this->get('config');
        $request             = $this->get('request');
        $userInfo            = $request->getUserInfo();
        $userInfo['referer'] = $request->getUrl();
        
        return $this->render('widget/iframe-content.html.php', array(
        
            'mobile'         => true,
            'ui'             => $config->data['appSettings'],
            'uiJson'         => str_replace("'", '&apos;', json_encode($config->data['appSettings'])),
            'defaultAvatars' => json_encode($this->getDefaultAvatars()),
            'info'           => str_replace("'", '&apos;', json_encode($userInfo)),
            'remoteDomain'   => $this->get('request')->getVar('domain')
        ));
    }
    
    public function customStyleAction()
    {
        $appSettings = $this->getCSSSettings();
        
        return $this->render('widget/custom-style.css.php', $appSettings, 'css', array(
        
            array('Cache-Control', 'no-cache, no-store, must-revalidate'),
            array('Pragma', 'no-cache'),
            array('Expires', '0')
        ));
    }
    
    public function themeStyleAction()
    {
        $appSettings = $this->getCSSSettings();
        
        return $this->render('../' . $appSettings['widgetTheme'] . '/style.css.php', $appSettings, 'css', array(
        
            array('Cache-Control', 'no-cache, no-store, must-revalidate'),
            array('Pragma', 'no-cache'),
            array('Expires', '0')
        ));
    }
    
    // Get settings for CSS customization
    
    protected function getCSSSettings()
    {
        $appSettings = $this->get('config')->data['appSettings'];
        $colors      = $this->get('color_calculator');
        
        // Calculate additional colors
        
        $appSettings['primaryColorDarker']    = $colors->sub($appSettings['primaryColor'],   '#2e3030');
        $appSettings['primaryColorDarker2']   = $colors->sub($appSettings['primaryColor'],   '#181a1b');
        $appSettings['secondaryColorDarker']  = $colors->sub($appSettings['secondaryColor'], '#10150c');
        $appSettings['secondaryColorLighter'] = $colors->add($appSettings['secondaryColor'], '#0e0d0f');
        
        return $appSettings;
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
}

?>

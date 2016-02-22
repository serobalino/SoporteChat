<?php

class Request extends Service
{
    // Fields

    private $method;
    private $getVars;
    private $postVars;
    private $files;
    private $ip;
    private $host;
    private $referer;

    private $uri;
    private $rootUri;
    private $rootUriFull;
    private $rootUriFullNoProto;
    private $route;
    private $rootUrl;

    // Constructor

    public function __construct()
    {
        $this->method     = strtoupper($_SERVER['REQUEST_METHOD']);
        $this->getVars    = $_GET;
        $this->postVars   = $_POST;
        $this->files      = $_FILES;
        $this->ip         = isset($_SERVER['REMOTE_ADDR'])     ? $_SERVER['REMOTE_ADDR'] : '';
        $this->remoteHost = isset($_SERVER['REMOTE_HOST'])     ? $_SERVER['REMOTE_HOST'] : '';
        $this->referer    = isset($_SERVER['HTTP_REFERER'])    ? $_SERVER['HTTP_REFERER'] : '';
        $this->browser    = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        $this->initFiles();

        $this->host               = $_SERVER['HTTP_HOST'];
        $this->protocol           = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https" : "http";
        $this->uri                = $_SERVER['REQUEST_URI'];
        $this->rootUri            = $_SERVER['SCRIPT_NAME'];
        $this->rootUriFull        = $this->getProtocol() . '://' . $this->getHost() . $this->rootUri;
        $this->rootUriFullNoProto = '//' . $this->getHost() . $this->rootUri;

        $components = parse_url($this->uri);

        if(isset($components['query']))
        {
            $this->query = $components['query'];
        }
        else
        {
            $this->query = '';
        }

        $i          = strpos($this->query, '&');
        $queryRoute = $i !== false ? substr($this->query, 0, $i) : $this->query;

        $this->route   = trim($queryRoute === false ? $this->query : $queryRoute, '/');
        $this->url     = $this->getProtocol() . '://' . $this->getHost() . $this->getUri();
        $this->rootUrl = '/' . trim(preg_replace('/\/php\/app(_\w+)?\.php.*/', '', $this->uri), '/');

        if(strlen($this->rootUrl) !== 1)
        {
            $this->rootUrl .= '/';
        }
    }

    // Methods

    public function getRoute()
    {
        return $this->route;
    }

    public function getServerName()
    {
        return $this->serverName;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getRootUri()
    {
        return $this->rootUri;
    }

    public function getRootUriFull($includeProto = true)
    {
        return $includeProto ? $this->rootUriFull : $this->rootUriFullNoProto;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getRootUrl()
    {
        return $this->rootUrl;
    }

    public function getVar($name, $safe = true)
    {
        return isset($this->getVars[$name]) ? ($safe ? $this->get('security')->escape($this->getVars[$name]) : $this->getVars[$name]) : null;
    }

    public function getVars()
    {
        return $this->getVars;
    }

    public function postVar($name, $safe = true)
    {
        return isset($this->postVars[$name]) ? ($safe ? $this->get('security')->escape($this->postVars[$name]) : $this->postVars[$name]) : null;
    }

    public function postVars()
    {
        return $this->postVars;
    }

    public function initFiles()
    {
        foreach($this->files as $id => $file)
        {
            $name = $file['name'];

            $this->files[$id]['ext'] = substr($name, strrpos($name, '.') + 1);
        }
    }

    public function file($name)
    {
        return isset($this->files[$name]) && $this->files[$name]['error'] === UPLOAD_ERR_OK ? $this->files[$name] : null;
    }

    public function files()
    {
        return $this->files;
    }

    public function getReferer()
    {
        return $this->referer;
    }

    public function getBrowser()
    {
        return $this->browser;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function getProtocol()
    {
        return $this->protocol;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function hasFile($name)
    {
        return $this->file($name) !== null;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function isGet()
    {
        return $this->getMethod() == 'GET';
    }

    public function isPost()
    {
        return $this->getMethod() == 'POST';
    }

    public function getUserInfo()
    {
        return array(

            'ip'      => $this->getIp(),
            'referer' => $this->getReferer(),/*
            'host'    => $this->getRemoteHost(),
            'browser' => $this->getBrowser()*/
        );
    }
}

?>

<?php

class Database extends Service
{
    // Constants

    const DEFAULT_PORT = 3306;

    // Fields

    private $db,
            $config,
            $logger;

    // Constructor

    public function onRegister()
    {
        parent::onRegister();

        // -----

        $this->config = $this->get('config');
        $this->logger = $this->get('logger');
        $this->reconnect();
    }

    // Methods

    public function connect($dsn, $user, $pass)
    {
        try
        {
            $this->db = new PDO($dsn, $user, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
            $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }
        catch(Exception $e)
        {
            // Don't throw exceptions if application is not yet installed

            $installed = isset($this->config->data['appSettings']['installed']) && $this->config->data['appSettings']['installed'];

            // Log

            $this->get('logger')->error($e->getMessage());

            if($installed)
            {
                throw $e;
            }

            return false;
        }

        return !empty($this->db);
    }

    public function reconnect()
    {
        $installed  = isset($this->config->data['appSettings']['installed']) && $this->config->data['appSettings']['installed'];
        $connection = $installed ? $this->config->data['dbConnection'] : $this->config->data['dbConnectionRaw'];

        $this->connect($connection, $this->config->data['dbUser'], $this->config->data['dbPassword']);
    }

    public function execute($q, $params = null)
    {
        $this->ensureValidConnection();

        // Split all queries into separate ones

        $queries = $this->splitQueries($q);

        // Execute all queries and return the result of the last one

        try
        {
            foreach($queries as $q)
            {
                $statement = $this->db->prepare($q);

                if($statement)
                {
                    $result = $statement->execute($params);
                }
            }

            return $result;
        }
        catch(Exception $e)
        {
            // Log

            $this->logger->error($e->getMessage());

            return false;
        }

        return false;
    }

    public function query($q, $params = null)
    {
        $this->ensureValidConnection();

        // Split all queries into separate ones

        $queries = $this->splitQueries($q);

        // Execute all queries and return the result of the last one

        try
        {
            foreach($queries as $q)
            {
                $statement = $this->db->prepare($q);

                if($statement)
                {
                    $statement->execute($params);

                    $result = $statement->fetchAll();
                }
            }

            return $result;
        }
        catch(Exception $e)
        {
            // Log

            $this->logger->error($e->getMessage());

            return false;
        }

        return false;
    }

    public function queryOne($q, $params = null)
    {
        $this->ensureValidConnection();

        try
        {
            $statement = $this->db->prepare($q);

            if($statement)
            {
                $statement->execute($params);

                return $statement->fetch();
            }
        }
        catch(Exception $e)
        {
            // Log

            $this->logger->error($e->getMessage());

            return false;
        }

        return false;
    }

    public function lastInsertId()
    {
        $this->ensureValidConnection();

        return $this->db->lastInsertId();
    }

    public function getTables()
    {
        $this->ensureValidConnection();

        $result = array();
        $tables = $this->query('SHOW TABLES');

        foreach($tables as $tableInfo)
        {
            $result[] = $tableInfo[0];
        }

        return $result;
    }

    protected function splitQueries($q)
    {
        return preg_split("/^\s*$/m", $q);
    }

    protected function ensureValidConnection()
    {
        if(!$this->db)
        {
            throw new Exception('Trying to use database with an invalid connection.');
        }
    }
}

?>

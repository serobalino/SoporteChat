<?php

class ModelValidation extends Service
{
    private $validation;
    
    public function onRegister()
    {
        parent::onRegister();
        
        // -----
        
        $this->validation = $this->get('validation');
    }
    
    public function validateMessage($message)
    {
        return $this->clearEmpty(array(
        
            'from' => $this->validation->validate($message['from'], array(
            
                'Value is blank'        => new NotBlankValidator(),
                'Value is not a number' => new NumberValidator()
            )),
            
            'to' => $this->validation->validate($message['to'], array(
            
                'Value is blank'        => new NotBlankValidator(),
                'Value is not a number' => new NumberValidator()
            )),
            
            'body' => $this->validation->validate($message['body'], array(
            
                'Value is blank' => new NotBlankValidator(),
            ))
        ));
    }
    
    public function validateUser($user, $checkPassword = true)
    {
        $data = array(
        
            'name' => $this->validation->validate($user['name'], array(
            
                'Value is blank' => new NotBlankValidator()
            )),
            
            'mail' => $this->validation->validate($user['mail'], array(
            
                'Value is blank'              => new NotBlankValidator(),
                'Value is not a valid e-mail' => new MailValidator()
            ))
        );
        
        if($checkPassword)
        {
            $data['password'] = $this->validation->validate($user['password'], array(
            
                'Value is blank'     => new NotBlankValidator(),
                'Value is too short' => new LengthValidator(6)
            ));
        }
        
        return $this->clearEmpty($data);
    }
    
    public function validateInstallConfig($config)
    {
        return $this->clearEmpty(array(
            
            'dbHost' => $this->validation->validate($config['dbHost'], array(
                
                'Value is blank' => new NotBlankValidator()
            )),
            
            'dbPort' => $this->validation->validate($config['dbPort'], array(
                
                'Value is blank'           => new NotBlankValidator(),
                'Value has to be a number' => new NumberValidator()
            )),
            
            'dbName' => $this->validation->validate($config['dbName'], array(
                
                'Value is blank' => new NotBlankValidator()
            )),
            
            'dbUser' => $this->validation->validate($config['dbUser'], array(
                
                'Value is blank' => new NotBlankValidator()
            )),
            /*
            'dbPassword' => $this->validation->validate($config['dbPassword'], array(
                
                'Value is blank' => new NotBlankValidator()
            )),
            */
            'superUser' => $this->validation->validate($config['superUser'], array(
                
                'Value is blank' => new NotBlankValidator()
            )),
            
            'superPass' => $this->validation->validate($config['superPass'], array(
                
                'Value is blank' => new NotBlankValidator()
            )),
            
            'superPassRepeat' => $this->validation->validate($config['superPassRepeat'], array(
                
                'Value is blank'      => new NotBlankValidator(),
                'Values do not match' => new EqualsValidator($config['superPass'])
            )),
            
            'appSettings' => array(

                'contactMail' => $this->validation->validate($config['appSettings']['contactMail'], array(
                    
                    'Value is blank'              => new NotBlankValidator(),
                    'Value is not a valid e-mail' => new MailValidator()
                ))
            )
        ));
    }
    
    public function validateContactData($data)
    {
        return $this->clearEmpty(array(
        
            'name' => $this->validation->validate($data['name'], array(
            
                'Value is blank' => new NotBlankValidator()
            )),
            
            'mail' => $this->validation->validate($data['mail'], array(
            
                'Value is blank'              => new NotBlankValidator(),
                'Value is not a valid e-mail' => new MailValidator()
            )),
            
            'question' => $this->validation->validate($data['question'], array(
            
                'Value is blank'     => new NotBlankValidator(),
                'Value is too short' => new LengthValidator(6)
            ))
        ));
    }
    
    public function validateLoginData($data)
    {
        return $this->clearEmpty(array(
        
            'name' => $this->validation->validate($data['name'], array(
            
                'Value is blank' => new NotBlankValidator()
            )),
            
            'mail' => $this->validation->validate($data['mail'], array(
            
                'Value is blank' => new NotBlankValidator(),
                'Value is not a valid e-mail' => new MailValidator()
            ))
        ));
    }

    public function validateConfig()
    {
        // No checks so far

        return array();
    }

    public function validateDb()
    {
        $errors = array('message' => 'Database structure is invalid');

        try
        {
            $db = $this->get('db');

            // Reconnect in case the database was created just now

            $db->reconnect();

            $tables = $db->getTables();

            $expectedTables = array(

                UserModel::repo()   ->getTableName(),
                MessageModel::repo()->getTableName(),
                DataModel::repo('') ->getTableName()
            );
        }
        catch(Exception $ex)
        {
            $errors['message'] = 'Database exception: ' . $ex->getMessage();

            return $errors;
        }
        
        if(count(array_diff($expectedTables, $tables)) !== 0)
        {
            $errors['message'] = 'Invalid table structure, actual tables: ' . join(', ', $tables) . ', expected tables: ' . join(', ', $expectedTables);

            return $errors;
        }

        return array();
    }
    
    private function clearEmpty($array)
    {
        // Clear empty entries
        
        $keys = array_keys($array);
        
        for($i = count($array) - 1; $i >= 0; $i--)
        {
            $key = $keys[$i];

            if(is_array($array[$key]))
            {
                $array[$key] = $this->clearEmpty($array[$key]);
            }
            
            if(empty($array[$key]))
            {
                unset($array[$key]);
            }
        }
        
        return $array;
    }
}

?>

<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\User;

class RegistrationFormValidation
{
    const MIN_USER_LENGTH = 3;
    
    private $validationErrors = [];
    
    public function __construct($username, $password, $first_name, $last_name, $phone, $company)
    {
        return $this->validate($username, $password, $first_name, $last_name, $phone, $company);
    }
    
    public function isGoodToGo()
    {
        return empty($this->validationErrors);
    }
    
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    private function validate($username, $password, $first_name, $last_name, $phone, $company)
    {
         // Password validation
        if (strlen($password) < 8) {
            $this->validationErrors[] = 'Password has to be at least 8 characters long';
        }
        else if (strlen($password) > 20) {
            $this->validationErrors[] = 'Password has to be less than 20 characters long';
        }
        else if (preg_match('/^[a-zA-Z0-9\`\~\!\@\#\$\%\^\&\*\(\)\_\-\+\=\[\]\\\|\{\}\;\:\'\"\.\,\/\<\>\?]+$/', $password) === 0) {
            $this->validationErrors[] = "Password can only contain letters, numbers and special characters";
        }

        if(empty($first_name)) {
            $this->validationErrors[] = "Please write in your first name";
        }

         if(empty($last_name)) {
            $this->validationErrors[] = "Please write in your last name";
        }

        if(empty($phone)) {
            $this->validationErrors[] = "Please write in your post code";
        }

        if (strlen($phone) != "8") {
            $this->validationErrors[] = "Phone number must be exactly eight digits";
        }

        if(strlen($company) > 0 && (!preg_match('/[^0-9]/',$company)))
        {
            $this->validationErrors[] = 'Company can only contain letters';
        }

        if (preg_match('/^[A-Za-z0-9_]+$/', $username) === 0) {
            $this->validationErrors[] = 'Username can only contain letters and numbers';
        }
        else if (strlen($username) > 20) {
            $this->validationErrors[] = 'Username has to be less than 20 characters long';
        }
        else if (strlen($username) < 5) {
            $this->validationErrors[] = 'Password has to be at least 5 characters long';
        }
    }
}

<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\Patent;

class PatentValidation {

    private $validationErrors = [];

    public function __construct($company, $title, $file) {
        return $this->validate($company, $title, $file);
    }

    public function isGoodToGo()
    {
        return \count($this->validationErrors) ===0;
    }

    public function getValidationErrors()
    {
    return $this->validationErrors;
    }

    public function validate($company, $title, $file)
    {
        if ($company == null) {
            $this->validationErrors[] = "Company/User needed";

        }
        if ($title == null) {
            $this->validationErrors[] = "Title needed";
        }
        if(!empty(pathinfo( $file, PATHINFO_EXTENSION)){
            $ext = pathinfo( $file, PATHINFO_EXTENSION);
            if( $ext == 'pdf' || $ext == 'doc' || $ext == 'txt' ) {
                return;
            }else{
                 $this->validationErrors[] = "The filetype has to be .pdf, .doc or .txt";
            }
        }
        return $this->validationErrors;
    }


}

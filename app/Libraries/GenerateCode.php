<?php

/**
 * GenerateCode Class
 */
class GenerateCode
{
    
    function generate_code(){
        
        // create the charset for the codes and jumble it all up
        $charset = str_shuffle(CHARSET);
        $code = substr($charset, 0, URL_LENGTH);
        
        // return a randomized code of the desired length
        return $code;
    }

}

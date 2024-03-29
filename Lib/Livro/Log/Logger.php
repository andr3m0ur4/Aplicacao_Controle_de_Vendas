<?php

namespace Livro\Log;

/**
 * Fornece uma interface abstrata para definição de algoritmos de LOG
 * @author André Moura
 */
abstract class Logger
{
    protected $filename;  // local do arquivo de LOG
    
    /**
     * Instancia um logger
     * @param $filename = local do arquivo de LOG
     */
    public function __construct ( $filename )
    {
        $this -> filename = $filename;
        // reseta o conteúdo do arquivo
        file_put_contents ( $filename, '' );
    }
    
    // define o método write como obrigatório
    abstract function write ( $message );
}

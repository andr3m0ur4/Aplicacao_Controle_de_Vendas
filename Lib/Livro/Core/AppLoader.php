<?php

namespace Livro\Core;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Carrega a classe da aplicação
 * @author André Moura
 */
class AppLoader
{
    protected $directories;
    
    /**
     * Adiciona um diretório a ser vasculhado
     */
    public function addDirectory($directory)
    {
        $this->directories[] = $directory;
    }
    
    /**
     * Registra o AppLoader
     */
    public function register()
    {
        spl_autoload_register([$this, 'loadClass']);
    }
    
    /**
     * Carrega uma classe
     */
    public function loadClass($class)
    {
        $folders = $this->directories;
        
        foreach ($folders as $folder) {
            if (file_exists("{$folder}/{$class}.php" )) {
                require_once "{$folder}/{$class}.php";
                return true;
            } else {
                if (file_exists($folder)) {
                    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder), RecursiveIteratorIterator::SELF_FIRST) as $entry) {
                        if (is_dir($entry)) {
                            if (file_exists("{$entry}/{$class}.php")) {
                                require_once "{$entry}/{$class}.php";
                                return true;
                            }
                        }
                    }
                }
            }
        }
    }
}

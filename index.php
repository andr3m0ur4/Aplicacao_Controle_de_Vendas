<?php

// library loader
require_once 'Lib/Livro/Core/ClassLoader.php';

$al = new Livro\Core\ClassLoader;
$al -> addNamespace ( 'Livro', 'Lib/Livro' );
$al -> register ( );

// aplication loader
require_once 'Lib/Livro/Core/AppLoader.php';

$al = new Livro\Core\AppLoader;
$al -> addDirectory ( 'App/Control' );
$al -> addDirectory ( 'App/Model' );
$al -> register ( );

// Vendor
$loader = require 'vendor/autoload.php';
$loader -> register ( );

// lê o conteúdo do template
$template = file_get_contents ( 'App/Templates/template.html' );

$content = '';
$class = 'Home';

if ( $_GET ) {
	$class = $_GET['class'];

	if ( class_exists ( $class ) ) {
		try {
			$pagina = new $class;			// instancia a classe

			ob_start ( );					// inicia controle de output

			$pagina -> show ( );			// exibe página

			$content = ob_get_contents ( );	// lê conteúdo gerado

			ob_end_clean ( );				// finaliza controle de output

		} catch (Exception $e) {
			$content = $e -> getMessage ( ) . '<br>' . $e -> getTraceAsString ( );
		}
	} else {
		$content = "Class <b>{$class}</b> not found";
	}
}

// injeta conteúdo gerado dentro do template
$output = str_replace ( '{content}', $content, $template );
$output = str_replace ( '{class}', $class, $output );

// exibe saída gerada
echo $output;

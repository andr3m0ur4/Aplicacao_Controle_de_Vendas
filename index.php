<?php

date_default_timezone_set('America/Sao_Paulo');

if (version_compare(PHP_VERSION, '7.0.0') == -1 ) {
    die('A versão mínima do PHP para rodar esta aplicação é: 7.0.0');
}

// Library loader
require_once __DIR__ . '/Lib/Livro/Core/ClassLoader.php';
$al = new Livro\Core\ClassLoader;
$al->addNamespace('Livro', 'Lib/Livro');
$al->register();

// Aplication loader
require_once __DIR__ . '/Lib/Livro/Core/AppLoader.php';
$al = new Livro\Core\AppLoader;
$al->addDirectory('App/Control');
$al->addDirectory('App/Model');
$al->register();

// Vendor
$loader = require_once __DIR__ . '/vendor/autoload.php';
$loader->register();

use Livro\Session\Session;

$content = '';

new Session;

if (Session::getValue('logged')) {
	// lê o conteúdo do template
	$template = file_get_contents('App/Templates/template.html');
	$class = 'Home';
} else {
	$template = file_get_contents('App/Templates/login.html');
	$class = 'LoginForm';
}

if ( isset ( $_GET['class'] ) AND Session::getValue ( 'logged' ) ) {
	$class = $_GET['class'];
}

if ( class_exists ( $class ) ) {
	try {
		$pagina = new $class;			// instancia a classe

		ob_start ( );					// inicia controle de output

		$pagina -> show ( );			// exibe página

		$content = ob_get_contents ( );	// lê conteúdo gerado

		ob_end_clean ( );				// finaliza controle de output

	} catch ( Exception $e ) {
		$content = $e -> getMessage ( ) . '<br>' . $e -> getTraceAsString ( );
	}
} else {
	$content = "Class <b>{$class}</b> not found";
}

// injeta conteúdo gerado dentro do template
$output = str_replace ( '{content}', $content, $template );
$output = str_replace ( '{class}', $class, $output );

// exibe saída gerada
echo $output;

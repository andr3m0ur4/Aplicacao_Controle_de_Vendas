<?php

use Livro\Control\Page;
use Livro\Widgets\Dialog\Message;
use Livro\Database\Transaction;
use Livro\Widgets\Container\Panel;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ProdutosReport extends Page
{
	
	public function __construct ( ) 
	{
		parent::__construct ( );
		
		$loader = new FilesystemLoader('App/Resources');
		$twig = new Environment($loader);
		$template = $twig->loadTemplate('produtos_report.html');
		
		// vetor de parâmetros para o template
		$replaces = array ( );
		
		// gerador Barcode em HTML
		$generator = new BarcodeGeneratorHTML();

		// gerador QRCode em SVG
		$renderer = new \BaconQrCode\Renderer\Image\Svg ( );
		$renderer -> setHeight ( 256 );
		$renderer -> setWidth ( 256 );
		$renderer -> setMargin ( 0 );

		$writer = new \BaconQrCode\Writer ( $renderer );

		try {
			// inicia transação com o banco livro
			Transaction::open ( 'livro' );

			$produtos = Produto::all ( );

			foreach ( $produtos as $produto ) {
				$produto -> barcode = $generator -> getBarcode ( 
					$produto -> id, 
					$generator::TYPE_CODE_128,
					5,
					100
				);
				$produto -> qrcode = $writer -> writeString ( 
					$produto -> id . ' ' . $produto -> descricao 
				);
			}

			$replaces['produtos'] = $produtos;

			// finaliza a transação
			Transaction::close ( );

		} catch ( Exception $e ) {
			new Message ( 'error', $e -> getMessage ( ) );
			Transaction::rollback ( );
		}

		$content = $template -> render ( $replaces );

		// cria um painél para conter o formulário
		$panel = new Panel ( 'Produtos' );
		$panel -> add ( $content );
		
		parent::add ( $panel );
		
	}
}

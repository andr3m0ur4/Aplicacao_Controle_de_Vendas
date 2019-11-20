<?php  

use Livro\Database\Transaction;
use Livro\Database\Record;
use Livro\Database\Criteria;
use Livro\Database\Repository;

class Venda extends Record
{
	const TABLENAME = 'venda';
	private $itens;
	private $cliente;

	public function set_cliente ( Pessoa $c )
	{
		$this -> cliente = $c;
		$this -> id_cliente = $c -> id;
	}

	public function get_cliente ( )
	{
		if ( empty ( $this -> cliente ) ) {
			$this -> cliente = new Pessoa ( $this -> id_cliente );
		}
		return $this -> cliente;	// Retorna o objeto instanciado
	}

	public function addItem ( Produto $p, $quantidade )
	{
		$item = new ItemVenda;
		$item -> produto = $p;
		$item -> preco = $p -> preco_venda;
		$item -> quantidade = $quantidade;
		$this -> itens[] = $item;
		$this -> valor_venda += ( $item -> preco * $quantidade );
	}

	public function store ( )
	{
		parent::store ( );	// armazena a venda

		// percorre os itens da venda
		foreach ( $this -> itens as $item ) {
			$item -> id_venda = $this -> id;
			$item -> store ( );	// armazena o item
		}
	}

	public function get_itens ( )
	{
		// instancia um repositório de Item
		$repositorio = new Repository ( 'ItemVenda' );

		// define o critério de filtro
		$criterio = new Criteria;
		$criterio -> add ( 'item_venda', '=', $this -> id );
		$this -> itens = $repositorio -> load ( $criterio );	// carrega a coleção
		return $this -> itens;	// retorna os itens
	}
}

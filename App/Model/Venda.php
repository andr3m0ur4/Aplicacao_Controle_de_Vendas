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
		$criterio -> add ( 'id_venda', '=', $this -> id );
		$this -> itens = $repositorio -> load ( $criterio );	// carrega a coleção
		return $this -> itens;	// retorna os itens
	}

	public static function getVendasMes ( )
	{
		$meses = array ( );
		$meses[1] = 'Janeiro';
		$meses[2] = 'Fevereiro';
		$meses[3] = 'Março';
		$meses[4] = 'Abril';
		$meses[5] = 'Maio';
		$meses[6] = 'Junho';
		$meses[7] = 'Julho';
		$meses[8] = 'Agosto';
		$meses[9] = 'Setembro';
		$meses[10] = 'Outubro';
		$meses[11] = 'Novembro';
		$meses[12] = 'Dezembro';

		$conn = Transaction::get ( );
		$result = $conn -> query ( "SELECT DATE_FORMAT(data_venda, '%m') AS mes, sum(valor_final) AS valor 
									FROM venda GROUP BY 1" 
		);
		$dataset = [];

		foreach ( $result as $row ) {
			$mes = $meses[( int ) $row['mes']];
			$dataset[$mes] = $row['valor'];
		}
		return $dataset;
	}

	public static function getVendasTipo ( )
	{
		$conn = Transaction::get ( );
        $result = $conn -> query ( "SELECT tipo.nome AS tipo, 
        								sum(item_venda.quantidade * item_venda.preco) AS total
                                    FROM venda, item_venda, produto, tipo
                                   	WHERE venda.id = item_venda.id_venda 
                                     	AND item_venda.id_produto = produto.id
                                     	AND produto.id_tipo = tipo.id
                                	GROUP BY 1"
        );
        
        $dataset = [];
        foreach ( $result as $row ) {
            $dataset[ $row['tipo'] ] = $row['total'];
        }
        
        return $dataset;
	}
}

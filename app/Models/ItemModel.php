<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{

    protected $table            = 'itens';
    protected $returnType       = 'App\Entities\Item';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'codigo_interno',
        'nome',
        'marca',
        'modelo',
        'preco_custo',
        'preco_venda',
        'estoque',
        'controla_estoque',
        'tipo',
        'ativo',
        'descricao'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';


    // Validation
    protected $validationRules = [
        'nome'    => 'required|max_length[124]|is_unique[itens.nome,id,{id}]',
        'preco_venda'    => 'required',
        'descricao'    => 'required',
    ];
    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo nome é obrigatorio',
            'max_length' => 'O campo nome não pode ser maior que 230 caractéres',
            'is_unique' => 'Esse nome já foi escolhido. Por favor informe outro.',
        ],
        'preco_venda' => [
            'required' => 'O campo preço de venda é obrigatorio'
        ],
        'descricao' => [
            'required' => 'O campo descrição é obrigatorio'
        ],
    ];

    // Callbacks
    protected $beforeInsert   = ['removeVirgulaValores'];
    protected $beforeUpdate   = ['removeVirgulaValores'];

    public function geraCodigoInternoItem(): string
    {
        do {

            $codigoInterno = random_string('numeric', 15);

            $this->where('codigo_interno', $codigoInterno);
        } while ($this->countAllResults() > 1);

        return $codigoInterno;
    }

    protected function removeVirgulaValores(array $data)
    {
        if (isset($data['data']['preco_custo'])) {

            $data['data']['preco_custo'] = str_replace(",", "", $data['data']['preco_custo']);
        }

        if (isset($data['data']['preco_venda'])) {

            $data['data']['preco_venda'] = str_replace(",", "", $data['data']['preco_venda']);
        }

        return $data;
    }

    public function pesquisaItens(string $term = null): array
    {
        if ($term === null) {
            return [];
        }

        $atributos = [
            'itens.*',
            'itens_imagens.imagem'
        ];

        $itens = $this->select($atributos)
            ->like('itens.nome', $term)
            ->orLike('itens.codigo_interno', $term)
            ->join('itens_imagens', 'itens_imagens.item_id = itens.id', 'LEFT')
            ->where('itens.ativo', true)
            ->where('itens.deletado_em', null)
            ->groupBy('itens.nome')
            ->findAll();
        
        if($itens === null){
            return [];
        }

        foreach($itens as $key => $item){

            if($item->tipo === 'produto' && $item->estoque < 1){
                unset($itens[$key]);
            }
        }

        return $itens;
    }

    public function realizaBaixaNoEstoqueDeProdutos(array $produtos){
        $arrayIds = array_column($produtos, 'id');

        $produtosEstoque = $this->select('id, estoque')->whereIn('id', $arrayIds)->asArray()->findAll();

        $arrayEstoque = [];

        foreach($produtos as $produto){

            foreach($produtosEstoque as $pEstoque){

                if($produto['id'] == $pEstoque['id']){
                    $novaQuantidadeEstoque = (int)$pEstoque['estoque'] - (int)$produto['quantidade'];

                    array_push($arrayEstoque, [
                        'id' => $pEstoque['id'],
                        'estoque' => $novaQuantidadeEstoque
                    ]);
                }
                
            }
        }

        return $this->updateBatch($arrayEstoque, 'id');
    }
}

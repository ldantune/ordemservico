<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemHistoricoModel extends Model
{

    protected $table            = 'itens_historico';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'usuario_id',
        'item_id',
        'acao',
        'atributos_alterados',
        'criado_em'
    ];


}

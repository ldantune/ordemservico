<?php

namespace App\Models;

use CodeIgniter\Model;

class OrdemResponsavelModel extends Model
{
    protected $table            = 'ordens_responsaveis';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'ordem_id',
        'usuario_abertura_id',
        'usuario_responsavel_id',
        'usuario_encerramento_id',
    ];
}

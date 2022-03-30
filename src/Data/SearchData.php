<?php
namespace App\Data;

use App\Entity\Competence;
use App\Entity\Personnalite;
use App\Entity\Etude;

class SearchData
{

    /**
     * @var string
     */
    public $q = '';

    /**
     * @var Competence[]
     */
    public $competence = [];

    /**
     * @var Personnalite[]
     */
    public $personnalite = [];

    /**
     * @var Etude[]
     */
    public $etude = [];
}
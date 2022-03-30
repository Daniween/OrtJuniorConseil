<?php

namespace App\Service;

use App\Repository\EtudiantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Globals extends AbstractController {

    /**
     * @var EtudiantRepository
     */
    private EtudiantRepository $etudiantRepository;

    public function __construct(EtudiantRepository $etudiantRepository)
    {
        $this->etudiantRepository = $etudiantRepository;
    }

    public function getCompletedProfil() {
        $user = $this->getUser();

        if ($user !== null) {
            if ($user->getName() !== null AND $user->getFirstName() !== null AND $user->getEtude() !== null) {
                return true;
            } else {
                return false;
            }
        }

    }

}


<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\FootballClub;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClubController extends FrontendController
{
    public function listAction(Request $request): Response
    {
        $clubs = new FootballClub\Listing();
        
        return $this->render('club/list.html.twig', [
            'clubs' => $clubs
        ]);
    }

    public function detailAction(Request $request, $slug): Response
    {
        $club = FootballClub::getById($slug);
        if (!$club) {
            throw $this->createNotFoundException("Football club not found");
        }

        return $this->render('club/detail.html.twig', [
            'club' => $club
        ]);
    }

}

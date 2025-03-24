<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\FootballClub;
use Pimcore\Model\DataObject\ClubPlayer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClubController extends FrontendController
{
    public function listAction(Request $request): Response
    {
        $clubs = new FootballClub\Listing();
        
        foreach ($clubs as $club) {
            $playerCount = (new ClubPlayer\Listing())
                ->filterByFootballclub($club)
                ->getTotalCount();

            $playerCounts[$club->getId()] = $playerCount;
        }

        return $this->render('club/list.html.twig', [
            'clubs' => $clubs,
            'playerCounts' => $playerCounts
        ]);
    }

    public function detailAction(Request $request, $slug): Response
    {
        $club = FootballClub::getById($slug);
        if (!$club) {
            throw $this->createNotFoundException("Football club not found");
        }

        $players = new ClubPlayer\Listing();
        $players->setCondition('footballclub__id = ?', [$club->getId()]);

        return $this->render('club/detail.html.twig', [
            'club' => $club,
            'players' => $players
        ]);
    }

}

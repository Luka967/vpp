<?php

namespace Skop\Controllers;

use Skop\Core\Controller;
use Skop\Core\ErrorPageException;
use Skop\Models\Domain\Theater;
use Skop\Models\TheaterModel;
use Skop\Models\TheaterSeatTypeModel;

class ManagerTheatersController extends Controller
{
    public function showTheaters()
    {
        $this->render('manage/theaters.twig', [
            'seatTypes' => TheaterSeatTypeModel::all(),
            'theaters' => TheaterModel::all(),
            'theaterColumns' => Theater::$columnTraits
        ]);
    }
}

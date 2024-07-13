<?php

namespace Skop\Controllers;
use Skop\Core\Controller;
use Skop\Core\ErrorPageException;
use Skop\Models\RepertoireModel;
use Skop\Models\TheaterModel;
use Skop\Models\TheaterSeatTypeModel;

class ReservationController extends Controller
{
    public function showPage()
    {
        $obj = RepertoireModel::withId($this->req->query['id']);
        if ($obj == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_REPERTOIRE);
        $theater = $obj->theater();

        $this->render('view/reservation.twig', [
            'repertoireEntry' => $obj,
            'theater' => $theater,
            'movie' => $obj->movie(),
            'theaterSeatingModel' => TheaterModel::seatingModelFor($theater),
            'seatTypes' => TheaterSeatTypeModel::all()
        ]);
    }
}

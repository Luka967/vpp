<?php

namespace Skop\Controllers;

use DateTime;
use Skop\Core\Controller;
use Skop\Core\ErrorPageException;
use Skop\Models\Domain\Ticket;
use Skop\Models\RepertoireModel;
use Skop\Models\TheaterModel;
use Skop\Models\TheaterSeatTypeModel;
use Skop\Models\TicketModel;

class ReservationController extends Controller
{
    private function ensureReachable(int $repertoireEntryId)
    {
        $repertoireEntry = RepertoireModel::withId($repertoireEntryId);
        if ($repertoireEntry == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_REPERTOIRE);

        if (!$repertoireEntry->hasReservationsOnline())
            throw new ErrorPageException(SKOP_ERROR_UNREACHABLE_REPERTOIRE);

        if (TicketModel::ofUserForRepertoireEntry($this->loggedInUser->id, $repertoireEntryId) != null)
            throw new ErrorPageException(SKOP_ERROR_EXISTING_TICKET);

        return $repertoireEntry;
    }
    public function showPage()
    {
        $repertoireEntry = $this->ensureReachable($this->req->query['id']);
        $theater = $repertoireEntry->theater();

        $this->render('view/reservation.twig', [
            'repertoireEntry' => $repertoireEntry,
            'theater' => $theater,
            'movie' => $repertoireEntry->movie(),
            'theaterSeatingModel' => TheaterModel::seatingModelFor($theater),
            'seatTypes' => TheaterSeatTypeModel::all()
        ]);
    }

    public function doInsert()
    {
        $repertoireEntry = $this->ensureReachable($this->req->query['id']);

        $maxSeats = SKOP_CONFIG['reservationMaxSeats'];
        $seatsPicked = explode(',', $this->req->data['seats_picked'], 1 + $maxSeats);
        if (count($seatsPicked) > $maxSeats)
            throw new ErrorPageException(SKOP_ERROR_TOOLARGE_TICKET);

        $seats = [];
        foreach ($seatsPicked as $seatStr)
        {
            $seatStrSplit = explode('-', $seatStr, 2);
            if (count($seatStrSplit) != 2)
                throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, 'Seats picked not conforming');
            if (
                filter_var($seatStrSplit[0], FILTER_VALIDATE_INT) === false
                || filter_var($seatStrSplit[1], FILTER_VALIDATE_INT) === false
            ) throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, 'Seats picked not conforming');
            $row = intval($seatStrSplit[0]);
            $col = intval($seatStrSplit[1]);
            $seats[] = [ 'row' => $row, 'col' => $col ];
        }

        $theater = $repertoireEntry->theater();
        $theaterSeating = TheaterModel::seatingFor($theater);

        $seatsFound = [];
        foreach ($seats as $seatIdx)
        {
            $row = $seatIdx['row'];
            $col = $seatIdx['col'];
            $found = false;
            foreach ($theaterSeating as $seat)
            {
                if ($seat->row != $row || $seat->column != $col)
                    continue;
                $seatsFound[] = $seat;
                $found = true;
                break;
            }
            if (!$found)
                throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "Of seats picked $row,$col does not actually exist");
        }

        // Da li je vikend se odnosi na stavku repertoara, koristi se za cenu karte
        $dayOfWeek = date('w', strtotime($repertoireEntry->screening_start));
        $isWeekend = $dayOfWeek == '0' || $dayOfWeek == '6';

        $priceTotal = 0;
        foreach ($seatsFound as $seat)
        {
            $seatType = TheaterSeatTypeModel::withId($seat->seat_type_id);
            $priceTotal += $isWeekend
                ? ($seatType->price_adult_weekend ?? $seatType->price_adult)
                : $seatType->price_adult;
        }

        $discountClub = $this->loggedInUser->discountClub();
        if ($discountClub != null)
        {
            $priceTotal = (float)$priceTotal * (1 - (float)$discountClub->discount / 100);
            $priceTotal = (int)$priceTotal;
        }

        $newTicket = new Ticket();
        $newTicket->user_id = $this->loggedInUser->id;
        $newTicket->repertoire_id = $repertoireEntry->id;
        $newTicket->discount_club_id = $discountClub?->id ?? null;
        $newTicket->price = $priceTotal;
        $newTicket->booked_at = date(DateTime::ATOM);

        $newTicket = TicketModel::insertOneWithPickedSeats($newTicket, $seatsFound);

        $this->render('view/reservationSuccess.twig', [
            'seats' => $seatsFound,
            'movie' => $repertoireEntry->movie(),
            'theater' => $theater,
            'discountClub' => $discountClub,
            'newTicket' => $newTicket
        ]);
    }

    public function doDelete()
    {
        $ticket = TicketModel::withId($this->req->query['id']);
        if ($ticket == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_TICKET);
        if (!$ticket->repertoireEntry()->hasReservationsOnline())
            throw new ErrorPageException(SKOP_ERROR_UNREACHABLE_REPERTOIRE);

        TicketModel::deleteOne($ticket->id);

        $this->render('view/reservationDeleteSuccess.twig', [
            'repertoireEntry' => $ticket->repertoireEntry()
        ]);
    }
}

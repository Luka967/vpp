<?php

namespace Skop\Controllers;

use Skop\Core\Controller;
use Skop\Core\ErrorPageException;
use Skop\Models\DiscountClubModel;
use Skop\Models\Domain\Ticket;
use Skop\Models\TicketModel;

class ManagerTicketsController extends Controller
{
    public function show()
    {
        $this->render('manage/tickets.twig', [
            'discountClubs' => DiscountClubModel::all(),
            'tickets' => TicketModel::allUnpaid(),
            'ticketObjectColumns' => Ticket::$columnTraits
        ]);
    }

    public function showEdit()
    {
        $obj = TicketModel::withId($this->req->query['id']);
        if ($obj == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_TICKET);

        $this->persistentFormData = (array)$obj;
        $this->show();
    }

    public function doUpdate()
    {
        $obj = TicketModel::withId($this->req->data['id']);
        if ($obj == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_TICKET);
        foreach ($obj::$columnTraits as $key => $traits)
            if ($traits['editable'])
                $obj->$key = $this->req->data[$key];

        TicketModel::updateOne($obj);

        $this->redirect('/manage/tickets');
    }
}

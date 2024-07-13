<?php

namespace Skop\Models;

use Skop\Core\Db;
use Skop\Core\Model;
use Skop\Models\Domain\Theater;
use Skop\Models\Domain\TheaterSeating;

class TicketModel extends Model
{
    protected static string $tableName = '`theaters`';
    const CLASS_PATH = 'Skop\\Models\\Domain\\Ticket';
}

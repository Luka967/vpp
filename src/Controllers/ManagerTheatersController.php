<?php

namespace Skop\Controllers;

use Skop\Core\Controller;
use Skop\Core\ErrorPageException;
use Skop\Models\Domain\Theater;
use Skop\Models\TheaterModel;
use Skop\Models\TheaterSeatTypeModel;

class ManagerTheatersController extends Controller
{
    protected function fetchSeatingModel(Theater $theater)
    {
        $seatTypes = [];
        foreach (TheaterSeatTypeModel::all() as $type)
            $seatTypes[$type->id] = $type;

        $seating = TheaterModel::seatingFor($theater);
        $rowCount = 0;
        $colCount = 0;
        foreach ($seating as $seat)
        {
            $rowCount = max($rowCount, 1 + $seat->row);
            $colCount = max($colCount, 1 + $seat->column);
        }

        $seatObjects = [];
        for ($i = 0; $i < $rowCount * $colCount; $i++)
            $seatObjects[] = '';
        foreach ($seating as $seat)
            $seatObjects[$seat->row * $colCount + $seat->column] = $seatTypes[$seat->seat_type_id]->name;

        return "$rowCount;$colCount;" . join(';', $seatObjects);
    }
    protected function fetchSeatingValue(string $value): array
    {
        // Validacija stringa za postavu bioskopske sale ne može biti potpuna u Request-u
        // bez poziva ka modelu, ali to želim izbeći baš prilikom validacije u Request-u
        $theaterSize = explode(';', $value, 3);
        if (count($theaterSize) != 3)
            throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, 'Seating incomplete or invalid');
        if (!ctype_digit($theaterSize[0]) || !ctype_digit($theaterSize[1]))
            throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, 'Seating invalid');

        $seatingRows = intval($theaterSize[0]);
        $seatingCols = intval($theaterSize[1]);
        if ($seatingRows < 0 || $seatingRows > 31 || $seatingCols < 0 || $seatingCols > 31)
            throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, 'Seating too small or too large');

        $selectedSeatTypes = explode(';', $theaterSize[2], $seatingRows * $seatingCols);
        if (count($selectedSeatTypes) != $seatingRows * $seatingCols)
            throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, 'Selected seating incomplete or invalid');

        $availableSeatTypes = [];
        foreach (TheaterSeatTypeModel::all() as $type)
            $availableSeatTypes[$type->name] = $type;

        $selectedSeatTypeObjects = [];

        foreach ($selectedSeatTypes as $type)
            if (empty($type))
                $selectedSeatTypeObjects[] = null;
            else if (!isset($availableSeatTypes[$type]))
                throw new ErrorPageException(SKOP_ERROR_UNKNOWN_SEATTYPE, "Selected seating includes unknown seat type '$type'");
            else
                $selectedSeatTypeObjects[] = $availableSeatTypes[$type];

        return [
            'rows' => $seatingRows,
            'cols' => $seatingCols,
            'objects' => $selectedSeatTypeObjects
        ];
    }

    public function showTheaters()
    {
        $this->render('manage/theaters.twig', [
            'seatTypes' => TheaterSeatTypeModel::all(),
            'theaters' => TheaterModel::all(),
            'theaterColumns' => Theater::$columnTraits
        ]);
    }

    public function showEditTheater()
    {
        $obj = TheaterModel::withId($this->req->query['id']);
        if ($obj == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_THEATER);

        $this->persistentFormData = (array)$obj;
        $this->persistentFormData['seating'] = $this->fetchSeatingModel($obj);

        $this->showTheaters();
    }

    public function doInsertTheater()
    {
        if (TheaterModel::withName($this->req->data['name']) != null)
            throw new ErrorPageException(SKOP_ERROR_CONFLICTING_THEATER);

        $seating = $this->fetchSeatingValue($this->req->data['seating']);

        $obj = new Theater();
        foreach ($obj::$columnTraits as $key => $_)
            $obj->$key = $this->req->data[$key];
        TheaterModel::insertOne($obj);

        $createdObj = TheaterModel::withName($obj->name);
        TheaterModel::updateSeatingFor($createdObj, $seating['rows'], $seating['cols'], $seating['objects']);

        $this->redirect('/manage/theaters');
    }

    public function doUpdateTheater()
    {
        $obj = TheaterModel::withId($this->req->data['id']);
        if ($obj == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_THEATER);

        $oldSeating = $this->fetchSeatingModel($obj);
        $newSeating = $this->fetchSeatingValue($this->req->data['seating']);

        TheaterModel::updateOne($obj);
        if ($oldSeating != $newSeating)
            TheaterModel::updateSeatingFor($obj, $newSeating['rows'], $newSeating['cols'], $newSeating['objects']);

        $this->redirect('/manage/theaters');
    }
}

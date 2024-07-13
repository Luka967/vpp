<?php

namespace Skop\Controllers;

use Skop\Core\Controller;
use Skop\Core\ErrorPageException;
use Skop\Models\DiscountClubModel;
use Skop\Models\Domain\User;
use Skop\Models\UserModel;

class ManagerUserController extends Controller
{
    public function show()
    {
        $this->render('manage/users.twig', [
            'discountClubs' => DiscountClubModel::all(),
            'users' => UserModel::all(),
            'userObjectColumns' => User::$columnTraits
        ]);
    }

    public function showEdit()
    {
        $obj = UserModel::withId($this->req->query['id']);
        if ($obj == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_USER);

        $this->persistentFormData = (array)$obj;
        $this->show();
    }

    public function doUpdate()
    {
        $obj = UserModel::withId($this->req->data['id']);
        if ($obj == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_USER);

        if (!$this->loggedInUser->isAdmin() && $obj->permissions !== $this->req->data['permissions'])
        {
            $this->logger->warning("$this->reqId tried changing permissions of an user when not permitted");
            throw new ErrorPageException(SKOP_ERROR_AUTH_NOPERMS, 'Cannot change permissions of users');
        }
        else if ($this->loggedInUser->isAdmin() && $obj->permissions !== $this->req->data['permissions'] && $obj->id == $this->loggedInUser->id)
        {
            $this->logger->notice("$this->reqId tried changing permissions of self");
            throw new ErrorPageException(SKOP_ERROR_AUTH_NOPERMS, 'Cannot change permissions of yourself');
        }

        foreach ($obj::$columnTraits as $key => $traits)
            if ($traits['editable'])
                $obj->$key = $this->req->data[$key];

        UserModel::updateOne($obj);
        $this->logger->info("$this->reqId updated user", [
            'object' => $obj
        ]);


        $this->redirect('/manage/users');
    }
}

<?php

namespace Skop\Controllers;
use Skop\Core\Controller;
use Skop\Core\ErrorPageException;
use Skop\Models\DiscountClubModel;
use Skop\Models\Domain\User;
use Skop\Models\UserModel;

class UserController extends Controller
{
    public function showLogin()
    {
        $this->render('user/login.twig');
    }
    public function doLogin()
    {
        $this->persistentFormData = [
            'email' => $this->req->data['email']
        ];

        $fetchedUser = UserModel::withEmail($this->req->data['email']);
        if ($fetchedUser == null)
            $this->render('user/login.twig', [ 'emailError' => "Korisnik sa ovim e-mailom ne postoji" ]);

        if (!password_verify($this->req->data['password'], $fetchedUser->password))
            $this->render('user/login.twig', [ 'passwordError' => "Pogrešna šifra" ]);

        $this->setLoggedInUser($fetchedUser);
        $this->redirect('/');
    }
    public function showRegister()
    {
        $this->render('user/register.twig');
    }
    public function doRegister()
    {
        $this->persistentFormData = [
            'first_name' => $this->req->data['first_name'],
            'last_name' => $this->req->data['last_name'],
            'email' => $this->req->data['email']
        ];

        if ($this->req->data['password'] != $this->req->data['password_repeat'])
            $this->render('user/register.twig', [ 'passwordError' => "Ponovljena šifra nije ista" ]);

        if (UserModel::withEmail($this->req->data['email']) != null)
            $this->render('user/register.twig', [ 'emailError' => "Već postoji korisnik sa ovim email-om" ]);

        $createdUser = new User();
        $createdUser->first_name = $this->req->data['first_name'];
        $createdUser->last_name = $this->req->data['last_name'];
        $createdUser->email = $this->req->data['email'];
        $createdUser->password = password_hash($this->req->data['password'], PASSWORD_BCRYPT);
        UserModel::insertOne($createdUser);

        $createdUser = UserModel::withEmail($this->req->data['email']);
        if ($createdUser == null)
            throw new ErrorPageException(0, 'Creating user failed');
        $this->setLoggedInUser($createdUser);
        $this->redirect('/');
    }

    public function showMe()
    {
        $discountClub = null;
        if ($this->loggedInUser->discount_club_id != null)
            $discountClub = DiscountClubModel::fromId($this->loggedInUser->discount_club_id);
        $this->render('user/me.twig', [
            'discountClub' => $discountClub
        ]);
    }
    public function doLogout()
    {
        $this->unsetLoggedInUser();
        $this->redirect('/');
    }
}

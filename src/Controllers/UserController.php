<?php

namespace Skop\Controllers;
use Skop\Core\Controller;
use Skop\Models\Domain\User;
use Skop\Models\UserModel;

class UserController extends Controller
{
    public function showLogin()
    {
        $this->render('login.twig');
    }
    public function doLogin()
    {
        $this->persistentFormData = [
            'email' => $this->req->data['email']
        ];

        $fetchedUser = UserModel::getOneByEmail($this->req->data['email']);
        if ($fetchedUser == null)
            $this->render('login.twig', [ 'emailError' => "Korisnik sa ovim e-mailom ne postoji" ]);

        if (!password_verify($this->req->data['password'], $fetchedUser->password))
            $this->render('login.twig', [ 'passwordError' => "Pogrešna šifra" ]);

        $this->setLoggedInUser($fetchedUser);
        $this->redirect('/');
    }
    public function doRegister()
    {
        $this->persistentFormData = [
            'first_name' => $this->req->data['first_name'],
            'last_name' => $this->req->data['last_name'],
            'email' => $this->req->data['email']
        ];

        if ($this->req->data['password'] != $this->req->data['password_repeat'])
            $this->render('register.twig', [ 'passwordError' => "Ponovljena šifra nije ista" ]);

        if (UserModel::getOneByEmail($this->req->data['email']))
            $this->render('register.twig', [ 'emailError' => "Već postoji korisnik sa ovim email-om" ]);

        $createdUser = new User();
        $createdUser->first_name = $this->req->data['first_name'];
        $createdUser->last_name = $this->req->data['last_name'];
        $createdUser->email = $this->req->data['email'];
        $createdUser->password = password_hash($this->req->data['password'], PASSWORD_BCRYPT);
        UserModel::createOne($createdUser);

        $createdUser = UserModel::getOneByEmail($this->req->data['email']);
        $this->setLoggedInUser($createdUser);
        $this->redirect('/');
    }
    public function showRegister()
    {
        $this->render('register.twig');
    }
}
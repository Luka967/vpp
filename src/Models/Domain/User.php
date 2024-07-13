<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;
use Skop\Models\DiscountClubModel;

final class User extends DomainObject
{
    const PERMISSIONS_RESTRICTED = -2;
    const PERMISSIONS_NONE = null;
    const PERMISSIONS_MANAGER = 1;
    const PERMISSIONS_ADMIN = 2;

    public string $email;
    public string $password;
    public int $permissions = 0;
    public ?int $discount_club_id = null;
    public string $first_name;
    public ?string $last_name;

    public function discountClub()
    {
        if ($this->discount_club_id == null)
            return null;
        return DiscountClubModel::withId($this->discount_club_id);
    }

    public static array $columnTraits = [
        'id'                => ['type' => 'int'               , 'editable' => false, 'partial' => true , 'min' => 1 , 'max' => INT_U_MAX],
        'email'             => ['type' => 'string|email'      , 'editable' => false, 'partial' => false, 'min' => 8 , 'max' => 127],
        'password'          => ['type' => 'string|password'   , 'editable' => false, 'partial' => false, 'min' => 60, 'max' => 60],
        'permissions'       => ['type' => 'int|permissions'   , 'editable' => true , 'partial' => false],
        'discount_club_id'  => ['type' => 'id|discountClub'   , 'editable' => true , 'partial' => null],
        'first_name'        => ['type' => 'string|alphabetic' , 'editable' => false, 'partial' => false, 'min' => 2 , 'max' => 127],
        'last_name'         => ['type' => 'string|alphabetic' , 'editable' => false, 'partial' => false, 'min' => 2 , 'max' => 31],
    ];

    public function isRestricted(): bool
    {
        return $this->permissions == User::PERMISSIONS_RESTRICTED;
    }
    public function isManager(): bool
    {
        return $this->permissions >= User::PERMISSIONS_MANAGER;
    }
    public function isAdmin(): bool
    {
        return $this->permissions >= User::PERMISSIONS_ADMIN;
    }
}

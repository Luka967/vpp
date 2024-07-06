<?php

namespace Skop\Models\Domain;

use Skop\Core\Serializable;

final class User
{
    use Serializable;

    const PERMISSIONS_RESTRICTED = -1;
    const PERMISSIONS_NONE = null;
    const PERMISSIONS_MANAGER = 1;
    const PERMISSIONS_ADMIN = 2;

    public int $id;
    public string $username;
    public ?int $permissions;
    public ?int $discount_club_id;
    public string $email;
    public string $password;
    public string $first_name;
    public ?string $last_name;
}

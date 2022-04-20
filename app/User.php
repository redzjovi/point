<?php

namespace App;

use App\Model\Auth\Role;
use App\Model\Project\Project;
use App\Model\Reward\Token;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $fist_name
 * @property string $last_name
 * @property null|string $address
 * @property null|string $phone
 * @property string $email
 * @property string $password
 * @property null|string $phone_confirmation_code
 * @property int $phone_confirmed
 * @property null|string $email_confirmation_code
 * @property int $email_confirmed
 * @property null|string $remember_token
 * @property null|int $created_by
 * @property null|int $updated_by
 * @property null|int $archived_by
 * @property null|string $created_at
 * @property null|string $updated_at
 * @property null|string $archived_at
 *
 * @property Collection<Role> $roles
 */
class User extends Authenticatable
{
    protected $connection = 'mysql';

    protected $appends = ['full_name'];

    use Notifiable, HasApiTokens, HasRoles;

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * The users that belong to the project.
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function findForPassport($username)
    {
        $field = filter_var($username, FILTER_VALIDATE_EMAIL)
            ? 'email' : 'name';

        return $this->where($field, $username)->first();
    }

    public function rewardTokens()
    {
        return $this->hasMany(Token::class);
    }
}

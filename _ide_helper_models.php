<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $identity
 * @property int $available_row
 * @property int $available_col
 * @property int $available_backseat
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Schedule> $schedules
 * @property-read int|null $schedules_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Seat> $seats
 * @property-read int|null $seats_count
 * @method static \Database\Factories\BusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bus whereAvailableBackseat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bus whereAvailableCol($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bus whereAvailableRow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bus whereIdentity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bus whereUpdatedAt($value)
 */
	class Bus extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $role_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereRoleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $route_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Schedule> $schedules
 * @property-read int|null $schedules_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereRouteName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereUpdatedAt($value)
 */
	class Route extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $time
 * @property int $bus_id
 * @property int $route_id
 * @property bool $closed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bus|null $bus
 * @property-read \App\Models\Route|null $route
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Seat> $seats
 * @property-read int|null $seats_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\ScheduleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereBusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereUpdatedAt($value)
 */
	class Schedule extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $bus_id
 * @property int $row_position
 * @property int $col_position
 * @property int $backseat_position
 * @property int|null $user_id
 * @property int $schedule_id
 * @property bool $verified
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bus|null $bus
 * @property-read \App\Models\Schedule|null $schedule
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\SeatFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seat whereBackseatPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seat whereBusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seat whereColPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seat whereRowPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seat whereScheduleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seat whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seat whereVerified($value)
 */
	class Seat extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $nim_nip
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $phone_number
 * @property string $address
 * @property int $credit_score
 * @property int $role_id
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Role|null $role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Schedule> $schedules
 * @property-read int|null $schedules_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Seat> $seats
 * @property-read int|null $seats_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreditScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNimNip($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}


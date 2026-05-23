<?php
// ===================== User.php =====================
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['couple_id', 'name', 'email', 'password', 'avatar', 'role'];

    protected $hidden = ['password', 'remember_token'];

    public function couple(): BelongsTo
    {
        return $this->belongsTo(Couple::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function targetSavings(): HasMany
    {
        return $this->hasMany(TargetSaving::class);
    }

    public function getAvatarDisplayAttribute(): string
    {
        return $this->avatar ?? '👤';
    }
}

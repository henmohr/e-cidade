<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * @var string
     */
    protected $table = 'configuracoes.db_usuarios';

    /**
     * @var string
     */
    protected $primaryKey = 'id_usuario';

    /**
     * @var array<string>
     */
    protected $fillable = [
        'nome', 'login', 'senha',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'senha', 'datatoken', 'remember_token',
    ];

    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function cgm(): BelongsTo
    {
        return $this->belongsTo(Cgm::class, 'z01_numcgm');
    }

    public function isAdmin(): bool
    {
        return $this->administrador === 1;
    }

    public function isActive(): bool
    {
        return $this->usuarioativo === 1;
    }

    public function getAuthPassword(): string
    {
        return $this->senha;
    }

    /**
     * @return string
     */
    public function getRememberTokenAttribute()
    {
        return $this->remember_token;
    }

    public function getCpfAttribute(): ?string
    {
        $cpf = $this->cgm?->z01_cgccpf ?? null;
        if (empty($cpf)) {
            return null;
        }

        return preg_replace('/\D+/', '', (string) $cpf);
    }

    /**
     * @param string $token
     * @return void
     */
    public function setRememberTokenAttribute(string $token)
    {
        $this->remember_token = $token;
        $this->save();
    }
}

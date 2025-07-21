<?php

namespace Illuminate\Http;

interface Request
{
    /**
     * @return \App\Models\Utilisateur|null
     */
    public function user($guard = null);
}
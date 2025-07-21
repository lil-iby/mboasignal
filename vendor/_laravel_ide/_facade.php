<?php

namespace Illuminate\Support\Facades;

interface Auth
{
    /**
     * @return \App\Models\Utilisateur|false
     */
    public static function loginUsingId(mixed $id, bool $remember = false);

    /**
     * @return \App\Models\Utilisateur|false
     */
    public static function onceUsingId(mixed $id);

    /**
     * @return \App\Models\Utilisateur|null
     */
    public static function getUser();

    /**
     * @return \App\Models\Utilisateur
     */
    public static function authenticate();

    /**
     * @return \App\Models\Utilisateur|null
     */
    public static function user();

    /**
     * @return \App\Models\Utilisateur|null
     */
    public static function logoutOtherDevices(string $password);

    /**
     * @return \App\Models\Utilisateur
     */
    public static function getLastAttempted();
}
<?php

namespace App\Traits;

trait AuthenticatableTrait
{
    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id_utilisateur';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->pass_utilisateur;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Get the name of the email address for the user.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email_utilisateur;
    }

    /**
     * Get the name of the email address for the user.
     *
     * @return string
     */
    public function getEmailForVerification()
    {
        return $this->email_utilisateur;
    }

    /**
     * Get the column name for the "email" field.
     *
     * @return string
     */
    public function getEmailName()
    {
        return 'email_utilisateur';
    }
}

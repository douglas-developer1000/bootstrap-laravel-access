<?php

namespace App\Services\Contracts;

use App\Libraries\Registration\Contracts\HandlerInterface;
use App\Libraries\Values\PhoneValue;
use App\Models\RegisterOrder;
use App\Models\RegisterApproval;
use \Illuminate\Support\Carbon;

interface RegistrationInterface
{
    /**
     * Define the existence of an User instance with email
     */
    public function existsUserByEmail(string $email): bool;

    /**
     * Search an RegisterOrder instance by email
     */
    public function findRegisterOrderByEmail(string $email): ?RegisterOrder;

    /**
     * Search an RegisterPermission instance by email
     */
    public function findRegisterApprovalByEmail(string $email): ?RegisterApproval;

    /**
     * Persist a new RegisterOrder instance inside database
     */
    public function createRegisterOrder(string $email, PhoneValue $phone): void;

    /**
     * Update the Model instance's phone into database
     */
    public function updateModelPhone(RegisterOrder|RegisterApproval $model, PhoneValue $phone): void;

    /**
     * Update an existent Register Approval instance into database
     */
    public function updateRegisterApproval(int $id, string $token, Carbon $expirationData): void;

    /**
     * Handle the User Account Register processes
     */
    public function handleRegister(string $email, PhoneValue $phone): void;

    /**
     * Store the registration handlers
     */
    public function setHandlers(HandlerInterface ...$handlers): RegistrationInterface;
}

<?php

namespace App\Services\Contracts;

use App\Libraries\Registration\Contracts\HandlerInterface;
use App\Models\RegisterOrder;
use App\Models\RegisterApproval;
use \Illuminate\Support\Carbon;

interface RegistrationServiceInterface
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
    public function createRegisterOrder(string $email, ?string $phone): void;

    /**
     * Update the Model instance's phone into database
     */
    public function updateModelPhone(RegisterOrder|RegisterApproval $model, ?string $phone): void;

    /**
     * Update an existent Register Approval instance into database
     */
    public function updateRegisterApproval(int $id, string $token, Carbon $expirationData): void;

    /**
     * Handle the User Account Register processes
     */
    public function handleRegister(string $email, ?string $phone): void;

    /**
     * Store the registration handlers
     */
    public function setHandlers(HandlerInterface ...$handlers): RegistrationServiceInterface;
}

<?php

declare(strict_types=1);

namespace App\Policies;

use App\Libraries\Enums\PermissionNameEnum;
use App\Models\PaymentCard;
use App\Models\User;

final class PaymentCardPolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @see ../../routes/custom/payment-cards.php
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionNameEnum::PAYMENT_CARD_INDEX);
    }

    /**
     * Determine whether the user can create models.
     *
     * @see ../../routes/custom/payment-cards.php
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionNameEnum::PAYMENT_CARD_CREATE);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @see ../../routes/custom/payment-cards.php
     */
    public function view(User $user, PaymentCard $card): bool
    {
        return $user->can(PermissionNameEnum::PAYMENT_CARD_SHOW) && (
            $user->isModelMine($card) ||
            $card->native == 1
        );
    }

    /**
     * Determine whether the user can edit the model.
     *
     * @see ../../routes/custom/payment-cards.php
     */
    public function edit(User $user, PaymentCard $card): bool
    {
        return
            $user->isModelMine($card) &&
            $user->can(PermissionNameEnum::PAYMENT_CARD_EDIT);
    }

    /**
     * Determine whether the user can store the model.
     *
     * @see ../../routes/custom/payment-cards.php
     */
    public function store(User $user): bool
    {
        return $user->can(PermissionNameEnum::PAYMENT_CARD_STORE);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @see ../../routes/custom/payment-cards.php
     */
    public function update(User $user, PaymentCard $card): bool
    {
        return
            $user->isModelMine($card) &&
            $user->can(PermissionNameEnum::PAYMENT_CARD_UPDATE);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @see ../../routes/custom/payment-cards.php
     */
    public function delete(User $user, PaymentCard $card): bool
    {
        return (
            $user->isModelMine($card) &&
            ! $card->deleted_at
        ) && $user->can(PermissionNameEnum::PAYMENT_CARD_DESTROY);
    }

    /**
     * @param  PaymentCard[]  $paymentCardList
     *
     * @see ../../routes/custom/payment-cards.php
     */
    public function deleteList(User $user, array $paymentCardList): bool
    {
        return collect($paymentCardList)->every(fn (PaymentCard $card) => (
            $this->delete($user, $card)
        ));
    }

    public function restore(User $user, PaymentCard $payCardDeleted): bool
    {
        return (
            $user->isModelMine($payCardDeleted) &&
            $payCardDeleted->deleted_at
        ) && $user->can(PermissionNameEnum::PAYMENT_CARD_RESTORE);
    }

    public function restoreList(User $user, array $paymentCardList): bool
    {
        return collect($paymentCardList)->every(fn (PaymentCard $card) => (
            $this->restore($user, $card)
        ));
    }
}

<?php

namespace CoquardCyrilleFreelance\SCBPaymentAPI;

interface Configurations
{
    /**
     * Get the merchant ID.
     *
     * @return string
     */
    public function getMerchant(): string;

    /**
     * Get terminal ID.
     *
     * @return string
     */
    public function getTerminal(): string;

    /**
     * Get the language code.
     *
     * @return string
     */
    public function getLanguage(): string;

    /**
     * Get the biller ID.
     *
     * @return string
     */
    public function getBiller(): string;

    /**
     * Get application ID.
     *
     * @return string
     */
    public function getApplicationId(): string;

    /**
     * Get application secret.
     *
     * @return string
     */
    public function getApplicationSecret(): string;

    /**
     * Get prefix.
     *
     * @return string
     */
    public function getPrefix(): string;

    /**
     * Is sandbox.
     *
     * @return bool
     */
    public function isSandbox(): bool;
}
<?php

namespace CoquardCyrilleFreelance\SCBPaymentAPI;

interface Configurations
{
    public function getMerchant(): string;

    public function getTerminal(): string;

    public function getLanguage(): string;

    public function getBiller(): string;

    public function getApplicationId(): string;

    public function getApplicationSecret(): string;

    public function getPrefix(): string;

    public function isSandbox(): bool;
}
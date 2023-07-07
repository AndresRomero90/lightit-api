<?php

namespace App\Interfaces;

use App\Models\ApiMedicToken;

interface ApiMedicAuthServiceInterface
{
    function getHashString(string $uri): string;
    function login(): ?ApiMedicToken;
    function getToken(): ApiMedicToken;
}

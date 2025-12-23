<?php

namespace tests\api\v2;

use Tests\Support\ApiTester;

class AuthCest
{
    public function testAuthSuccess(ApiTester $I): void
    {
        $I->wantTo('Получить успешный ответ, от ендпоинта, требующего авторизацию');

        $token = getenv('API_AUTH_KEY');

        $I->haveHttpHeader('X-API-KEY', $token);

        $I->sendGet('/months');
        $I->seeResponseCodeIs(200);
    }

    public function testAuthFail(ApiTester $I): void
    {
        $I->wantTo('Получить отказ, от ендпоинта, требующего авторизацию');

        $token = 'not_a_token';

        $I->haveHttpHeader('X-API-KEY', $token);

        $I->sendGet('/months');
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsValidOnJsonSchemaString(json_encode([
            'type' => 'object',
            'properties' => [
                'message' => [
                    'type' => 'string'
                ],
                'x-debug-tag' => [
                    'type' => 'string'
                ],
            ],
            'required' => [
                'message', 'x-debug-tag'
            ],
        ]));
    }
}
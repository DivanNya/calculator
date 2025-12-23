<?php

namespace tests\api\v1;

use Tests\Support\ApiTester;

class TonnageCest
{
    private array $headers = [
        'X-API-KEY' => null,
    ];

    public function __construct()
    {
        $this->headers['X-API-KEY'] = getenv('API_AUTH_KEY');
    }

    public function testTonnagesList(ApiTester $I): void
    {
        $I->wantTo('Получить список тоннажей');

        $fixture = file_get_contents(__DIR__ . '/../../Support/Data/fixtures/tonnages.json');

        $data = json_decode($fixture, true);

        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $I->sendGet('/tonnages');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson($data);
    }

    public function testTonnagesCreate(ApiTester $I): void
    {
        $I->wantTo('Добавить новый тоннаж');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $newValue = 125;

        $I->sendPost('/tonnages', ['value' => $newValue]);
        $I->seeResponseCodeIs(201);
    }

    public function testTonnagesCreateDouble(ApiTester $I): void
    {
        $I->wantTo('Повторно добавить существующий тоннаж');

        $double = 125;

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $I->sendPost('/tonnages', ['tonnage' => $double]);
        $I->seeResponseCodeIs(400);
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

    public function testTonnageDelete(ApiTester $I): void
    {
        $I->wantTo('Удалить тоннаж');

        $id = 5;

        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $I->sendDelete('/tonnages/' . $id);
        $I->seeResponseCodeIs(204);
    }

    public function testNotExistingTonnageDelete(ApiTester $I): void
    {
        $I->wantTo('Удалить несуществующий тоннаж');

        $value = 150;

        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $I->sendDelete('/tonnages/' . $value);
        $I->seeResponseCodeIs(404);
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
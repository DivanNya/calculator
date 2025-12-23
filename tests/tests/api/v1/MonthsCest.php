<?php

namespace tests\api\v1;

use Tests\Support\ApiTester;

class MonthsCest
{
    private array $headers = [
        'X-API-KEY' => null,
    ];

    public function __construct()
    {
        $this->headers['X-API-KEY'] = getenv('API_AUTH_KEY');
    }

    public function testMonthsList(ApiTester $I): void
    {
        $I->wantTo('Получить список месяцев');

        $fixture = file_get_contents(__DIR__ . '/../../Support/Data/fixtures/months.json');

        $data = json_decode($fixture, true);

        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $I->sendGet('/months');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson($data);
    }

    public function testMonthsCreate(ApiTester $I): void
    {
        $I->wantTo('Добавить новый месяц');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $newValue = 'декабрь';

        $I->sendPost('/months', ['name' => $newValue]);
        $I->seeResponseCodeIs(201);
    }

    public function testMonthsCreateDouble(ApiTester $I): void
    {
        $I->wantTo('Повторно добавить существующий месяц');

        $double = 'декабрь';

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $I->sendPost('/months', ['month' => $double]);
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

    public function testMonthDelete(ApiTester $I): void
    {
        $I->wantTo('Удалить месяц');

        $id = 7;

        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $I->sendDelete('/months/' . $id);
        $I->seeResponseCodeIs(204);
    }

    public function testNotExistingMonthDelete(ApiTester $I): void
    {
        $I->wantTo('Удалить несуществующий месяц');

        $id = -1;

        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $I->sendDelete('/months/' . $id);
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
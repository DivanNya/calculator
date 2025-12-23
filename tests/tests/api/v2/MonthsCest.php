<?php

namespace tests\api\v2;

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

        foreach ($data as $monthItem) {
            $I->haveInDatabase('months', $monthItem);
        }

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
        $I->seeInDatabase('months', ['name' => $newValue]);
    }

    public function testMonthsCreateBadRequestOnNull(ApiTester $I): void
    {
        $I->wantTo('Передать некорректное название месяца');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $I->sendPost('/months', ['month' => null]);
        $I->seeResponseCodeIs(400);
    }

    public function testMonthsCreateDouble(ApiTester $I): void
    {
        $I->wantTo('Повторно добавить существующий месяц');

        $double = 'июнь';

        $I->haveInDatabase('months', ['name' => $double]);
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

        $value = 'июль';

        $id = $I->haveInDatabase('months', ['name' => $value]);
        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $I->sendDelete('/months/' . $id);
        $I->seeResponseCodeIs(204);
        $I->dontSeeInDatabase('months', ['name' => $value]);
    }

    public function testNotExistingMonthDelete(ApiTester $I): void
    {
        $I->wantTo('Удалить несуществующий месяц');

        $value = 'июль';
        $id = -1;

        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $I->sendDelete('/months/' . $id);
        $I->seeResponseCodeIs(404);
        $I->dontSeeInDatabase('months', ['name' => $value]);
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
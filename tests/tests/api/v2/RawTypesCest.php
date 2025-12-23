<?php

namespace tests\api\v2;

use Tests\Support\ApiTester;

class RawTypesCest
{
    private array $headers = [
        'X-API-KEY' => null,
    ];

    public function __construct()
    {
        $this->headers['X-API-KEY'] = getenv('API_AUTH_KEY');
    }

    public function testRawTypesList(ApiTester $I): void
    {
        $I->wantTo('Получить список типов сырья');

        $fixture = file_get_contents(__DIR__ . '/../../Support/Data/fixtures/raw_types.json');

        $data = json_decode($fixture, true);

        foreach ($data as $rawTypeItem) {
            $I->haveInDatabase('raw_types', $rawTypeItem);
        }

        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $I->sendGet('/types');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson($data);
    }

    public function testRawTypesCreate(ApiTester $I): void
    {
        $I->wantTo('Добавить новый тип сырья');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $newValue = 'лактоза';

        $I->sendPost('/types', ['name' => $newValue]);
        $I->seeResponseCodeIs(201);
        $I->seeInDatabase('raw_types', ['name' => $newValue]);
    }

    public function testRawTypesCreateDouble(ApiTester $I): void
    {
        $I->wantTo('Повторно добавить существующий тип сырья');

        $double = 'эфиры';

        $I->haveInDatabase('raw_types', ['name' => $double]);
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $I->sendPost('/types', ['name' => $double]);
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

    public function testRawTypeDelete(ApiTester $I): void
    {
        $I->wantTo('Удалить тип сырья');

        $value = 'белки';

        $id = $I->haveInDatabase('raw_types', ['name' => $value]);
        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $I->sendDelete('/types/' . $id);
        $I->seeResponseCodeIs(204);
        $I->dontSeeInDatabase('raw_types', ['name' => $value]);
    }

    public function testNotExistingRawTypeDelete(ApiTester $I): void
    {
        $I->wantTo('Удалить несуществующий тип сырья');

        $value = 'белки';
        $id = -1;

        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $I->sendDelete('/types/' . $id);
        $I->seeResponseCodeIs(404);
        $I->dontSeeInDatabase('raw_types', ['name' => $value]);
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
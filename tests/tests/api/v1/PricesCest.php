<?php

namespace tests\api\v1;

use Tests\Support\ApiTester;

class PricesCest
{
    private array $headers = [
        'X-API-KEY' => null,
    ];

    public function __construct()
    {
        $this->headers['X-API-KEY'] = getenv('API_AUTH_KEY');
    }

    public function testGetPriceSuccess(ApiTester $I): void
    {
        $I->wantTo('Рассчитать цену');

        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $filter = [
            'month_id' => 1,
            'tonnage_id' => 2,
            'raw_type_id' => 2
        ];

        $I->sendGet('/prices', ['filter' => $filter]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['price' => 145]);
    }

    public function testGetPriceFail(ApiTester $I): void
    {
        $I->wantTo('Получить ответ о ненайденной стоимости');

        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $filter = [
            'month_id' => 1,
            'tonnage_id' => 2,
            'raw_type_id' => 2
        ];

        $I->sendGet('/prices', ['filter' => $filter]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsValidOnJsonSchemaString('[]');
    }

    public function testPriceCreate(ApiTester $I): void
    {
        $I->wantTo('Добавить новый прайс');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $newPrice = [
            'month_id' => 1,
            'tonnage_id' => 1,
            'raw_type_id' => 2,
            'price' => 176
        ];

        $I->sendPost('/prices', $newPrice);
        $I->seeResponseCodeIs(201);
    }

    public function testPriceUpdate(ApiTester $I): void
    {
        $I->wantTo('Изменить существующий прайс');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $id = 1;
        $newPrice = [
            'month_id' => 1,
            'tonnage_id' => 2,
            'raw_type_id' => 2,
            'price' => 176
        ];

        $I->sendput('/prices/' . $id , $newPrice);

        $I->seeResponseCodeIs(204);
    }

    public function testNotExistingPriceUpdate(ApiTester $I): void
    {
        $I->wantTo('Изменить несуществующий прайс');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $id = -1;
        $newPrice = [
            'month_id' => 1,
            'tonnage_id' => 2,
            'raw_type_id' => 2,
            'price' => 176
        ];

        $I->sendput('/prices/' . $id , $newPrice);

        $I->seeResponseCodeIs(404);
    }
}
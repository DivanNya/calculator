<?php

namespace tests\api\v2;

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

        $I->haveInDatabase('months', ['name' => 'месяц1']);
        $I->haveInDatabase('tonnages', ['value' => 150]);
        $I->haveInDatabase('raw_types', ['name' => 'сырье1']);

        $monthId = $I->grabFromDatabase('months', 'id', ['name' => 'месяц1']);
        $tonnageId = $I->grabFromDatabase('tonnages', 'id', ['value' => 150]);
        $typeId = $I->grabFromDatabase('raw_types', 'id', ['name' => 'сырье1']);

        $I->haveInDatabase('prices', [
            'month_id' => $monthId,
            'tonnage_id' => $tonnageId,
            'raw_type_id' => $typeId,
            'price' => 256
        ]);

        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $I->sendGet(
            '/prices',
            [
                'filter' => [
                    'month_id' => $monthId,
                    'tonnage_id' => $tonnageId,
                    'raw_type_id' => $typeId,
                ]
            ]
        );
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['price' => 256]);
    }

    public function testGetPriceFail(ApiTester $I): void
    {
        $I->wantTo('Получить ответ о ненайденной стоимости');

        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $I->sendGet('/prices', ['filter' => ['month_id' => 1, 'tonnage_id' => 0,'raw_type_id' => 3]]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsValidOnJsonSchemaString('[]');
    }

    public function testPriceCreate(ApiTester $I): void
    {
        $I->wantTo('Добавить новый прайс');

        $I->haveInDatabase('months', ['name' => 'месяц2']);
        $I->haveInDatabase('tonnages', ['value' => 250]);
        $I->haveInDatabase('raw_types', ['name' => 'сырье2']);
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $monthId = $I->grabFromDatabase('months', 'id', ['name' => 'месяц2']);
        $tonnageId = $I->grabFromDatabase('tonnages', 'id', ['value' => 250]);
        $typeId = $I->grabFromDatabase('raw_types', 'id', ['name' => 'сырье2']);

        $newPrice = [
            'month_id' => $monthId,
            'tonnage_id' => $tonnageId,
            'raw_type_id' => $typeId,
            'price' => 176
        ];

        $I->sendPost('/prices', $newPrice);
        $I->seeResponseCodeIs(201);


        $I->seeInDatabase('prices', [
            'month_id' => $monthId,
            'tonnage_id' => $tonnageId,
            'raw_type_id' => $typeId,
            'price' => 176
        ]);
    }

    public function testPriceUpdate(ApiTester $I): void
    {
        $I->wantTo('Изменить существующий прайс');

        $I->haveInDatabase('months', ['name' => 'месяц3']);
        $I->haveInDatabase('tonnages', ['value' => 252]);
        $I->haveInDatabase('raw_types', ['name' => 'сырье3']);

        $monthId = $I->grabFromDatabase('months', 'id', ['name' => 'месяц3']);
        $tonnageId = $I->grabFromDatabase('tonnages', 'id', ['value' => 252]);
        $typeId = $I->grabFromDatabase('raw_types', 'id', ['name' => 'сырье3']);

        $priceId = $I->haveInDatabase('prices', [
            'month_id' => $monthId,
            'tonnage_id' => $tonnageId,
            'raw_type_id' => $typeId,
            'price' => 250,
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $I->sendPatch('/prices/' . $priceId, ['price' => 300]);

        $I->seeResponseCodeIs(204);

        $I->seeInDatabase('prices', [
            'month_id' => $monthId,
            'tonnage_id' => $tonnageId,
            'raw_type_id' => $typeId,
            'price' => 300
        ]);
    }

    public function testPriceDelete(ApiTester $I): void
    {
        $I->wantTo('Удалить прайс');

        $I->haveInDatabase('months', ['name' => 'месяц4']);
        $I->haveInDatabase('tonnages', ['value' => 253]);
        $I->haveInDatabase('raw_types', ['name' => 'сырье4']);

        $monthId = $I->grabFromDatabase('months', 'id', ['name' => 'месяц4']);
        $tonnageId = $I->grabFromDatabase('tonnages', 'id', ['value' => 253]);
        $typeId = $I->grabFromDatabase('raw_types', 'id', ['name' => 'сырье4']);

        $priceId = $I->haveInDatabase('prices', [
            'month_id' => $monthId,
            'tonnage_id' => $tonnageId,
            'raw_type_id' => $typeId,
            'price' => 258,
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-API-KEY', $this->headers['X-API-KEY']);

        $I->sendDelete('/prices/' . $priceId);

        $I->seeResponseCodeIs(204);

        $I->dontSeeInDatabase('prices', [
            'month_id' => $monthId,
            'tonnage_id' => $tonnageId,
            'raw_type_id' => $typeId,
            'price' => 257
        ]);
    }
}
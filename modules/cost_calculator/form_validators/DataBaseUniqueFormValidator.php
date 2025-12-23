<?php

declare(strict_types=1);

namespace app\modules\cost_calculator\form_validators;

use Monoelf\Framework\resource\DataBaseResourceDataFilter;
use Monoelf\Framework\resource\form_request\FormRequestInterface;
use Monoelf\Framework\validator\FormValidatorInterface;

final class DataBaseUniqueFormValidator implements FormValidatorInterface
{
    public function __construct(
        private readonly DataBaseResourceDataFilter $databaseResourceDataFilter,
        private array $options
    ) {
        $this->databaseResourceDataFilter->setAccessibleFields($this->options['attributes']);
        $this->databaseResourceDataFilter->setAccessibleFilters($this->options['attributes']);
        $this->databaseResourceDataFilter->setResourceName($this->options['ruleOptions']['resource']);
    }

    public function validate(FormRequestInterface $form): void
    {
        $filter = array_intersect_key($form->getValues(), array_flip($this->options['attributes']));

        $data = $this->databaseResourceDataFilter->filterOne(['filter' => $filter]);

        if ($data === null) {
            return;
        }

        $message = 'Ключ не уникален: ' . var_export($filter, true);

        foreach ($filter as $key => $value) {
            $form->addError($key, $message);
        }
    }
}

<?php

namespace Helldar\Yandex\GoodsPrices\Services\Items\Types;

use DOMElement;
use Helldar\Yandex\GoodsPrices\Interfaces\Item;
use Helldar\Yandex\GoodsPrices\Traits\Items\BaseMethods;
use Helldar\Yandex\GoodsPrices\Traits\Validator;
use Helldar\Yandex\GoodsPrices\Traits\Xml;

abstract class BaseType implements Item
{
    use Xml, Validator, BaseMethods;

    protected $type = 'vendor.model';

    private $required_items = [];

    private $items = [];

    public function __construct()
    {
        $this->requiredItems('url', 'price', 'currencyId', 'categoryId');
    }

    public function get(): DOMElement
    {
        $offer = $this->offer();

        $this->validate($this->items, $this->rules());

        return $this->make($offer);
    }

    protected function requiredItems(string ...$values)
    {
        foreach ($values as $value) {
            \array_push($this->required_items, $value);
        }
    }

    protected function addItem($key, $value)
    {
        $this->items[$key] = $value;
    }

    protected function rules(): array
    {
        return [];
    }

    private function item($key): array
    {
        return $this->items[$key] ?? null;
    }

    private function offer()
    {
        $id        = $this->item('id');
        $available = $this->item('available');
        $type      = $this->type;

        return $this->xmlItem('offer', null, \compact('id', 'available', 'type'));
    }

    private function make(DOMElement $offer): DOMElement
    {
        foreach ($this->items as $key => $value) {
            if (\in_array($key, ['id', 'type', 'available'])) {
                continue;
            }

            $item = $this->xmlItem($key, $value);

            $this->xmlAppendChild($offer, $item);
        }

        return $offer;
    }
}
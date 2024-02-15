<?php

use srag\Plugins\UdfEditor\Exception\UDFNotFoundException;

class xudfContentElement extends ActiveRecord
{
    public const DB_TABLE_NAME = 'xudf_element';

    public function getConnectorContainerName(): string
    {
        return self::DB_TABLE_NAME;
    }

    public function create(): void
    {
        $element = self::orderBy('sort')->first();
        $sort = $element ? ($element->getSort() + 10) : 10;
        $this->setSort($sort);
        parent::create();
    }

    public static function find($primary_key, array $add_constructor_args = []): ?self
    {
        return parent::find($primary_key, $add_constructor_args);
    }


    /**
     * @con_has_field    true
     * @con_sequence     true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     * @con_is_primary   true
     */
    protected int $id;

    /**
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected int $obj_id;

    /**
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     */
    protected int $sort;

    /**
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     */
    protected bool $is_separator = false;

    /**
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     */
    protected int $udf_field;

    /**
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       256
     */
    protected string $title;

    /**
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       256
     */
    protected string $description;

    /**
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     */
    protected bool $is_required = false;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getObjId(): int
    {
        return $this->obj_id;
    }

    public function setObjId(int $obj_id): void
    {
        $this->obj_id = $obj_id;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }

    public function isSeparator(): bool
    {
        return $this->is_separator;
    }

    public function setIsSeparator(bool $is_separator): void
    {
        $this->is_separator = $is_separator;
    }

    public function getUdfFieldId(): int
    {
        return $this->udf_field;
    }

    public function setUdfFieldId(int $udf_field): void
    {
        $this->udf_field = $udf_field;
    }


    /**
     * @throws UDFNotFoundException
     */
    public function getUdfFieldDefinition(): array
    {
        $definition = ilUserDefinedFields::_getInstance()->getDefinition($this->getUdfFieldId());
        if (!is_array($definition) || empty($definition)) {
            throw new UDFNotFoundException('udf with id ' . $this->getUdfFieldId() . ' could not be found and was probably deleted');
        }

        return $definition;
    }


    /**
     * @throws UDFNotFoundException
     */
    public function getTitle(): string
    {
        if (!$this->isSeparator()) {
            return $this->getUdfFieldDefinition()['field_name'];
        }

        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function isRequired(): bool
    {
        return (bool) $this->is_required;
    }

    public function setIsRequired(bool $is_required): void
    {
        $this->is_required = $is_required;
    }
}

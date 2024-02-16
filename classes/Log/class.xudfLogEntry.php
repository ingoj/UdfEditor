<?php

class xudfLogEntry extends ActiveRecord
{
    public const TABLE_NAME = 'xudf_log_entry';

    public function getConnectorContainerName(): string
    {
        return self::TABLE_NAME;
    }


    /**
     * @con_has_field    true
     * @con_sequence     true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     * @con_is_primary   true
     */
    protected ?int $id;

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
     * @con_is_notnull   true
     */
    protected int $usr_id;

    /**
     * @con_has_field  true
     * @con_fieldtype  clob
     * @con_is_notnull true
     */
    protected array $values = [];

    /**
     * @con_has_field  true
     * @con_fieldtype  timestamp
     * @con_index      true
     * @con_is_notnull true
     */
    protected ilDateTime $timestamp;


    /**
     * @throws ilDateTimeException
     */
    public static function createNew(int $obj_id, int $usr_id, array $values): self
    {
        $new = new self();
        $new->obj_id = $obj_id;
        $new->usr_id = $usr_id;
        $new->values = $values;
        $new->timestamp = new ilDateTime(time(), IL_CAL_UNIX);
        $new->create();

        return $new;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getObjId(): int
    {
        return $this->obj_id;
    }

    public function getUsrId(): int
    {
        return $this->usr_id;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getTimestamp(): ilDateTime
    {
        return $this->timestamp;
    }


    /**
     * @return false|int|mixed|string
     */
    public function sleep($field_name): mixed
    {
        switch ($field_name) {
            case 'values':
                return json_encode($this->values, JSON_THROW_ON_ERROR);
            case 'timestamp':
                return $this->timestamp->get(IL_CAL_DATETIME);
            default:
                return parent::sleep($field_name);
        }
    }


    /**
     * @return ilDateTime|mixed
     * @throws ilDateTimeException
     */
    public function wakeUp($field_name, $field_value): mixed
    {
        switch ($field_name) {
            case 'values':
                return json_decode($field_value, true, 512, JSON_THROW_ON_ERROR);
            case 'timestamp':
                return new ilDateTime($field_value, IL_CAL_DATETIME);
        }

        return parent::wakeUp($field_name, $field_value);
    }
}

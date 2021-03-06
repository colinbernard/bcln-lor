<?php

namespace local_lor\item\property;

use dml_exception;
use stdClass;

class category implements editable_property
{

    const TABLE = 'local_lor_category';
    const LINKING_TABLE = 'local_lor_item_categories';

    /**
     * Get categories for this item
     *
     * @param int $itemid
     *
     * @return array of objects containing category ID and name properties
     * @throws dml_exception
     */
    public static function get_item_data(int $itemid)
    {
        global $DB;

        return $DB->get_records_sql(
            sprintf(
                "
            SELECT c.id, c.name
            FROM {%s} c
            JOIN {%s} ic ON ic.categoryid = c.id
            WHERE ic.itemid = :itemid
        ",
                self::TABLE,
                self::LINKING_TABLE
            ),
            ['itemid' => $itemid]
        );
    }

    /**
     * Create a category (from settings page)
     *
     * @param stdClass $data
     *
     * @return bool
     * @throws dml_exception
     */
    public static function create(stdClass $data)
    {
        global $DB;

        return $DB->insert_record(self::TABLE, (object)['name' => $data->name]);
    }

    /**
     * Update a category (from settings page)
     *
     * @param int      $id
     * @param stdClass $data
     *
     * @return bool
     * @throws dml_exception
     */
    public static function update(int $id, stdClass $data)
    {
        global $DB;
        $data->id = $id;

        return $DB->update_record(self::TABLE, $data);
    }

    /**
     * Delete a category (from settings page)
     *
     * @param int $id
     *
     * @return bool
     * @throws dml_exception
     */
    public static function delete(int $id)
    {
        global $DB;

        return $DB->delete_records(self::TABLE, ['id' => $id])
               && $DB->delete_records(
                self::LINKING_TABLE,
                ['categoryid' => $id]
            );
    }

    public static function save_item_form(int $itemid, stdClass $data)
    {
        global $DB;

        self::delete_for_item($itemid);

        foreach ($data->categories as $categoryid) {
            $DB->insert_record(
                self::LINKING_TABLE,
                (object)[
                    'itemid'     => $itemid,
                    'categoryid' => $categoryid,
                ],
                false
            );
        }
    }

    /**
     * Get all categories
     *
     * @return array
     * @throws dml_exception
     */
    public static function get_all()
    {
        global $DB;

        return $DB->get_records(self::TABLE);
    }

    /**
     * Get all categories as assoc. array id => name
     *
     * @return array
     * @throws dml_exception
     */
    public static function get_all_menu()
    {
        global $DB;

        return $DB->get_records_menu(self::TABLE, null, 'name ASC', 'id,name');
    }

    /**
     * Get a single category
     *
     * @param int $id The ID of the category
     *
     * @return mixed
     * @throws dml_exception
     */
    public static function get(int $id)
    {
        global $DB;

        return $DB->get_record(self::TABLE, ['id' => $id]);
    }

    public static function delete_for_item(int $itemid)
    {
        global $DB;

        return $DB->delete_records(self::LINKING_TABLE, ['itemid' => $itemid]);
    }
}

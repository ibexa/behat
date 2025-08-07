<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\ContentData;

class FieldTypeNameConverter
{
    private static $FIELD_TYPE_MAPPING = [
        'ibexa_author' => 'Authors',
        'ibexa_boolean' => 'Checkbox',
        'ibexa_content_query' => 'Content query',
        'ibexa_object_relation' => 'Content relation (single)',
        'ibexa_object_relation_list' => 'Content relations (multiple)',
        'ibexa_country' => 'Country',
        'ibexa_date' => 'Date',
        'ibexa_datetime' => 'Date and time',
        'ibexa_email' => 'Email address',
        'ibexa_binaryfile' => 'File',
        'ibexa_form' => 'Form',
        'ibexa_float' => 'Float',
        'ibexa_isbn' => 'ISBN',
        'ibexa_image' => 'Image',
        'ibexa_image_asset' => 'Image Asset',
        'ibexa_integer' => 'Integer',
        'ibexa_keyword' => 'Keywords',
        'ibexa_landing_page' => 'Landing Page',
        'ezpage' => 'Layout',
        'ibexa_gmap_location' => 'Map location',
        'ibexa_matrix' => 'Matrix',
        'ibexa_media' => 'Media',
        'ezsrrating' => 'Rating',
        'ibexa_richtext' => 'Rich text',
        'ibexa_selection' => 'Selection',
        'ibexa_text' => 'Text block',
        'ibexa_string' => 'Text line',
        'ibexa_time' => 'Time',
        'ibexa_url' => 'URL',
        'ibexa_user' => 'User account',
        'ibexa_taxonomy_entry_assignment' => 'Taxonomy Entry Assignment',
    ];

    public static function getFieldTypeNameByIdentifier(string $fieldTypeIdentifier): string
    {
        return static::$FIELD_TYPE_MAPPING[$fieldTypeIdentifier];
    }

    public static function getFieldTypeIdentifierByName(string $fieldTypeName): string
    {
        $identifier = array_search($fieldTypeName, static::$FIELD_TYPE_MAPPING);
        if ($identifier === false) {
            throw new \InvalidArgumentException("Field type name '{$fieldTypeName}' not found.");
        }

        return (string)$identifier;
    }
}

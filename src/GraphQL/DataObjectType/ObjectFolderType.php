<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\General\FolderType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

class ObjectFolderType extends FolderType
{

    /**
     * AssetFolderType constructor.
     * @param Service $graphQlService
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, $config = [], $context = [])
    {
        parent::__construct($graphQlService, ['name' => 'object_folder'], $context);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $objectTreeType = $this->getGraphQlService()->buildGeneralType('object_tree');
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\Element('object', $this->getGraphQLService());

        $config['fields'] = [
            'id' => [
                'name' => 'id',
                'type' => Type::id(),
            ],
            'key' => Type::string(),
            'fullpath' => [
                'type' => Type::string()
            ],
            'creationDate' => Type::int(),
            'modificationDateDate' => Type::int(),
            'parent' => [
                'type' => $objectTreeType,
                'resolve' => [$resolver, 'resolveParent'],
            ],
            'children' => [
                'type' => Type::listOf($objectTreeType),
                'args' => [
                    'objectTypes' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => 'list of object types (object, variant, folder)'
                    ],
                ],
                'resolve' => [$resolver, 'resolveChildren'],
            ],
            '_siblings' => [
                'type' => Type::listOf($objectTreeType),
                'args' => [
                    'objectTypes' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => 'list of object types (object, variant, folder)'
                    ],
                ],
                'resolve' => [$resolver, 'resolveSiblings'],
            ],
        ];

    }
}

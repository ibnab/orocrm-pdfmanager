<?php

namespace Ibnab\Bundle\PmanagerBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class IbnabPmanagerBundle implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->createPDFTemplateTable($schema);
        $this->createPDFTemplateTranslationTable($schema);
        /** Foreign keys generation **/
        //$this->addPDFTemplateTableForeignKeys($schema);
        //$this->addPDFTemplateTranslationTableForeignKeys($schema);
    }


    /**
     * Create ibnab_pmanager_template table
     *
     * @param Schema $schema
     */
    protected function createPDFTemplateTable(Schema $schema)
    {
        $table = $schema->createTable('ibnab_pmanager_template');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('isSystem', 'boolean', []);
        $table->addColumn('isEditable', 'boolean', []);
        $table->addColumn('name', 'string',['length' => 255]);
        $table->addColumn('description', 'text', ['notnull' => false]);
        $table->addColumn('css', 'text' , ['notnull' => false]);
        $table->addColumn('format', 'string', ['notnull' => false ,'length' => 4]);
        $table->addColumn('unit', 'string', ['notnull' => false ,'length' => 4]);
        $table->addColumn('orientation', 'string', ['notnull' => false ,'length' => 2]);
        $table->addColumn('auteur', 'string', ['notnull' => false ,'length' => 30]);
        $table->addColumn('margintop', 'integer', ['notnull' => false]);
        $table->addColumn('marginleft', 'integer', ['notnull' => false ]);
        $table->addColumn('marginright', 'integer', ['notnull' => false]);
        $table->addColumn('marginbottom', 'integer', ['notnull' => false]);
        $table->addColumn('autobreak', 'boolean', ['notnull' => false]);
        $table->addColumn('user_owner_id', 'integer', []);
        $table->addColumn('organization_id', 'integer', []);
        $table->addColumn('parent', 'integer', ['notnull' => false]);
        $table->addColumn('content', 'text', ['notnull' => false]);
        $table->addColumn('entityName', 'string', ['length' => 255]);
        $table->addColumn('type', 'string', ['length' => 20]);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['name', 'entityName'], 'UQ_NAME');
        $table->addIndex(['name'], 'pmanager_pdftemplate_name_idx', []);
        $table->addIndex(['isSystem'], 'pmanager_pdftemplate_is_system_idx', []);
        $table->addIndex(['entityName'], 'pmanager_pdftemplate_entity_name_idx', []);
        $table->addIndex(['user_owner_id'], 'IDX_ibnabEE2DB9449EB185F9', []);
        $table->addIndex(['organization_id'], 'IDX_ibnabEE2DB94432C8A3DE', []);
        $table = $schema->getTable('ibnab_pmanager_template');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            []
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['user_owner_id'],
            ['id'],
            []
        );      
    }

    /**
     * Create ibnab_pmanager_translation table
     *
     * @param Schema $schema
     */
    protected function createPDFTemplateTranslationTable(Schema $schema)
    {
        $table = $schema->createTable('ibnab_pmanager_translation');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('object_id', 'integer', ['notnull' => false]);
        $table->addColumn('locale', 'string', ['length' => 8]);
        $table->addColumn('field', 'string', ['length' => 32]);
        $table->addColumn('content', 'text', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['locale','object_id','field'], 'IDX_ibnabB520E901232D562BU');
        $table->addIndex(['object_id'], 'IDX_ibnabB520E901232D562B', []);
        $table = $schema->getTable('ibnab_pmanager_translation');
        $table->addForeignKeyConstraint(
            $schema->getTable('ibnab_pmanager_template'),
            ['object_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
    }
}


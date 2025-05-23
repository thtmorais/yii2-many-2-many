<?php

namespace thtmorais\many2many;

use yii\helpers\ArrayHelper;
use yii\db\BaseActiveRecord;

/**
 * Class Many2ManyBehavior
 * @package thtmorais\many2many
 */
class Many2ManyBehavior extends \yii\base\Behavior
{
    /**
     * Attribute that represents the primary key of the source model.
     * @var string
     */
    public string $id = 'id';

    /**
     * Attribute that contains the relationships to be saved in the destination model.
     * @var string
     */
    public string $attribute;

    /**
     * Class name of the related model.
     * @var string
     */
    public string $relatedModel;

    /**
     * Attribute that represents the primary key of the related model.
     * @var string
     */
    public string $relatedModelId = 'id';

    /**
     * Attribute in the related model that stores the relationship with the source model.
     * @var string
     */
    public string $relatedModelAttribute;

    /**
     * Whether to validate the related model before saving.
     * @var bool
     */
    public bool $relatedModelValidate = true;

    /**
     * {@inheritDoc}
     */
    public function events(): array
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            BaseActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    /**
     * Handles the insertion of related models after the source model is inserted.
     * @return void
     */
    public function afterInsert()
    {
        $model = $this->owner;

        if (is_array($model->{$this->attribute})) {
            foreach ($model->{$this->attribute} as $attributes) {
                $relatedModel = new $this->relatedModel();

                foreach ($attributes as $key => $value) {
                    $relatedModel->{$key} = $value;
                }

                $relatedModel->{$this->relatedModelAttribute} = $model->{$this->id};

                $relatedModel->save($this->relatedModelValidate);
            }
        }
    }

    /**
     * Handles the update of related models after the source model is updated.
     * @return void
     */
    public function afterUpdate()
    {
        $model = $this->owner;

        $oldModels = $this->relatedModel::find()->where([$this->relatedModelAttribute => $model->{$this->id}])->all();

        foreach ($oldModels as $oldModel) {
            $exist = false;

            if (is_array($model->{$this->attribute})) {
                foreach ($model->{$this->attribute} as $attributes) {
                    if (ArrayHelper::keyExists($this->relatedModelId, $attributes)) {
                        if ($oldModel->{$this->relatedModelId} == ArrayHelper::getValue($attributes, $this->relatedModelId)) {
                            $exist = true;

                            break;
                        }
                    }
                }
            }

            if (!$exist) {
                if ($oldModel->refresh()) {
                    $oldModel->delete();
                }
            }
        }

        if (is_array($model->{$this->attribute})) {
            foreach ($model->{$this->attribute} as $attributes) {
                $relatedModel = new $this->relatedModel();

                if (ArrayHelper::keyExists($this->relatedModelId, $attributes)) {
                    foreach ($oldModels as $oldModel) {
                        if ($oldModel->{$this->relatedModelId} == ArrayHelper::getValue($attributes,$this->relatedModelId)) {
                            $relatedModel = $oldModel;
                        }
                    }
                }

                foreach ($attributes as $key => $value) {
                    $relatedModel->{$key} = $value;
                }

                $relatedModel->{$this->relatedModelAttribute} = $model->{$this->id};

                $relatedModel->save($this->relatedModelValidate);
            }
        }
    }

    /**
     * Handles the deletion of related models after the source model is deleted.
     * @return void
     */
    public function afterDelete()
    {
        $model = $this->owner;

        $oldModels = $this->relatedModel::find()->where([$this->relatedModelAttribute => $model->{$this->id}])->all();

        foreach ($oldModels as $oldModel) {
            if ($oldModel->refresh()) {
                $oldModel->delete();
            }
        }
    }
}

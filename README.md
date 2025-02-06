# Many To Many (many-2-many) implementation for Yii PHP Framework 2

## Overview
`Many2ManyBehavior` is a Yii PHP Framework 2 behavior that facilitates managing many-2-many relationships between models. It automatically handles the insertion, update, and deletion of related models.

## Installation
Install via Composer:

```shell
composer require thtmorais/many2many
```

## Usage

Attach `Many2ManyBehavior` to your model:

```php
use thtmorais\many2many\Many2ManyBehavior;

class Model extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => Many2ManyBehavior::class,
                'id' => 'id',
                'attribute' => 'relations',
                'relatedModel' => RelatedModel::class,
                'relatedModelId' => 'id',
                'relatedModelAttribute' => 'model_id',
                'relatedModelValidate' => true
            ],
        ];
    }
}
```

## Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `string` | Primary key of the source model. Defaults to `'id'`. |
| `attribute` | `string` | Attribute containing the relationships to be saved. |
| `relatedModel` | `string` | Class name of the related model. |
| `relatedModelId` | `string` | Primary key of the related model. Defaults to `'id'`. |
| `relatedModelAttribute` | `string` | Foreign key in the related model referencing the source model. |
| `relatedModelValidate` | `bool` | Whether to validate the related model before saving. Defaults to `true`. |

## Events Handled

- **`EVENT_AFTER_INSERT`**: Saves related models after the source model is inserted.
- **`EVENT_AFTER_UPDATE`**: Updates related models when the source model is updated.
- **`EVENT_AFTER_DELETE`**: Deletes related models when the source model is deleted.

## Methods

### `afterInsert()`
Handles the insertion of related models when the source model is inserted.

### `afterUpdate()`
Handles the update of related models when the source model is updated.

### `afterDelete()`
Deletes related models when the source model is deleted.

## Example Usage

```php
$model = new Model();

$model->relations = [
    ['id' => 1, 'name' => 'Item 1'],
    ['id' => 2, 'name' => 'Item 2']
];

$model->save();
```

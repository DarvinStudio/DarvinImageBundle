Сущность-изображение
====================

## Описание

Сущность-изображение - сущность, класс которой наследуется от базового "Darvin\ImageBundle\Entity\Image\AbstractImage".
 Все такие сущности хранятся в одной таблице базы данных, что позволяет работать с ними, как с одной сущностью. В
 частности это реализовано в менеджере изображений панели администрирования (при подключенном
 "darvinstudio/darvin-admin-bundle"). Каждому классу соответствует определенная группа размеров изображений. При
 подключенной панели администрирования размеры группы можно редактировать в разделе настроек. Эти размеры можно
 переопределить для конкретного изображения в менеджере изображений, причем они будут приоритетнее размеров группы. При
 применении к изображению какого-либо фильтра, указывается один из размеров группы или конкретного изображения.

## Twig

К сущности можно применить следующие фильтры Twig:

- **image_original** - возвращает URL оригинального изображения;
- **image_crop** - обрезает изображение до нужного размера и возвращает URL результата;
- **image_resize** - пропорционально масштабирует изображение до нужного размера и возвращает URL результата.

Также существует функция Twig, проверяющая существование файла изображения - "image_exists()".

## Создание

**1. Если для сущности требуется новая группа размеров, создаем конфигурацию изображений.**

Класс конфигурации изображений должен наследоваться от класса "Darvin\ConfigBundle\Configuration\AbstractConfiguration"
 и реализовывать интерфейс "Darvin\ImageBundle\Configuration\ImageConfigurationInterface".

Параметр конфигурации представляет собой массив объектов "Darvin\ImageBundle\Size\Size". Если предполагается
 редактирование конфигурации в панели администрирования, можно воспользоваться существующей формой "darvin_image_size",
 указав ее в опциях модели параметра.

Пример реализации:

```php
use Darvin\ConfigBundle\Configuration\AbstractConfiguration;
use Darvin\ConfigBundle\Parameter\ParameterModel;
use Darvin\ImageBundle\Configuration\ImageConfigurationInterface;
use Darvin\ImageBundle\Size\Size;

class ImageConfiguration extends AbstractConfiguration implements ImageConfigurationInterface
{
    public function getModel()
    {
        return array(
            new ParameterModel(
                'image_sizes',
                ParameterModel::TYPE_ARRAY,
                array(
                    'common' => new Size('common', 256, 256),
                ),
                array(
                    'form' => array(
                        'options' => array(
                            'type' => 'darvin_image_size',
                        ),
                    ),
                )
            ),
        );
    }

    public function getName()
    {
        return 'app_image';
    }

    public function getImageSizes()
    {
        return $this->__call(__FUNCTION__);
    }

    public function isImageSizesGlobal()
    {
        return false;
    }

    public function getImageSizeGroupName()
    {
        return 'app';
    }
```

- метод "getName()" должен возвращать название конфигурации, уникальное в рамках всех конфигураций;

- "getImageSizes()" должен проксировать вызов на магический метод, соответствующий названию параметра (в примере параметр
 называется "image_sizes", поэтому метод вызывает "getImageSizes", т. е. магический метод с названием, равным значению
 константы "__FUNCTION__");

- метод "isImageSizesGlobal()" определяет, является ли группа размеров изображений глобальной;

- "getImageSizeGroupName()" возвращает название группы размеров изображений.

**2. Создаем класс сущности, который наследуется от "Darvin\ImageBundle\Entity\Image\AbstractImage":**

```php
use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Image extends AbstractImage
{
    public function getSizeGroupName()
    {
        return 'app';
    }
}
```

Метод "getSizeGroupName()" должен возвращать название группы размеров.

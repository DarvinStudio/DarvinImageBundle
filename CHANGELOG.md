6.1.0: Make "ext-zip" (allows to archive images) optional.

6.1.1: "Darvin\ImageBundle\Form\Type\ImageType" form type: added options "filters", "width" and "height", which help
 to generate recommended image size description. "filters" can be string or array containing Imagine filter set name(s).
 If multiple Imagine filters are provided, filter with biggest thumbnail size will be used. "width" and "height" have
 higher priority than "filters".
 
Admin section configuration examples:

```yaml
form:
    edit:
        fields:
            image:
                type: Darvin\ImageBundle\Form\Type\ImageType
                options:
                    data_class: AppBundle\Entity\PostImage
                    filters:    [ post_image, post_photo ]
```

```yaml
form:
    edit:
        fields:
            image:
                type: Darvin\ImageBundle\Form\Type\ImageType
                options:
                    data_class: AppBundle\Entity\PostImage
                    width:      640
                    height:     480
```

6.1.2: Extract image size describer service.

6.1.3: Image size describer: auto-detect Imagine filter set names if null passed as filters.

6.1.4: Image form type: merge descriptions.

6.1.5: Init update image dimensions command only in "dev" environment.

6.1.6: Add list orphan images command.

6.1.7: Use placeholder image in "image_original" Twig filter.

7.0.0:

- remove redundant commands;

- remove image configuration interface;

- remove image size classes;

- make AbstractImage::getUploadDir() abstract and static.

7.0.3: Add imageable entity form type.

7.0.4: Add image exterminate action.

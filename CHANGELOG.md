6.1.0: Make "ext-zip" (allows to archive images) optional.

6.1.1: "Darvin\ImageBundle\Form\Type\Image\ImageType" form type: added options "filters", "width" and "height", which help
 to generate recommended image size description. "filters" can be string or array containing Imagine filter set name(s).
 If multiple Imagine filters are provided, filter with biggest thumbnail size will be used. "width" and "height" have
 higher priority than "filters".
 
Admin section configuration examples:

```yaml
form:
    edit:
        fields:
            image:
                type: Darvin\ImageBundle\Form\Type\Image\ImageType
                options:
                    data_class: AppBundle\Entity\PostImage
                    filters:    [ post_image, post_photo ]
```

```yaml
form:
    edit:
        fields:
            image:
                type: Darvin\ImageBundle\Form\Type\Image\ImageType
                options:
                    data_class: AppBundle\Entity\PostImage
                    width:      640
                    height:     480
```

6.1.2: Extract image size describer service.

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

7.0.5: Rename cache resolvers to escape collisions.

7.0.8:
 
- Move service configs to "services" dir.

- Replace "empty()" calls with null comparisons.

7.0.9: Register interfaces for autoconfiguration.

7.0.16: Move part of functionality from copy cloned image file event subscriber to Utils.

7.1.0: Do not join image translations because of error "The discriminator column "dtype" is missing for "Darvin\ImageBundle\Entity\Image\ImageTranslation" using the DQL alias "images_translations"."

7.1.1: Change temporary files directory from "/tmp" to "%kernel.project_dir%/var/tmp".

7.1.2: Add help to "name" field of image edit form.

7.1.3: Support WebP.

7.1.4: Support SVG.

7.2.0: Allow to convert image format:

```yaml
darvin_image:
    output_formats:
        jpg:  ~
        webp: ~
```

```twig
{# Image in original format #}
<img src="{{ page.image|image_filter('page_main') }}">
{# Image in JPG #}
<img src="{{ page.image|image_filter('page_main__jpg') }}">
{# Image in WebP #}
<img src="{{ page.image|image_filter('page_main__webp') }}">
```

7.2.1: Show mtime in list orphans command.

7.2.2: Allow list orphans command to work in production environment.

7.2.3: Disable CSRF protection.

8.0.0: Extract File bundle.

8.0.1: Alias public services.

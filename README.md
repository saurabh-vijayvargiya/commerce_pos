Commerce Point of Sale (POS)
============================

## Setup

### UPC
If you wish to use UPC fields, 
you'll want to add the UPC field to your form config. 
It is added to the default product variation type by default 
but not displayed. If you wish to add it to any other product 
variations, just add it as you would any other field.

## Contributing
### Naming Conventions
Submodule names should be prefixed with "Commerce POS" to keep things
organized and tidy.

## JQuery.print

This module depends on the `jQuery.print` plugin which should reside in your 
site's `/libraries` directory.

We can install `jQuery.print` using composer or by downloading manually.

### Composer method

#### How can I add js/css libraries using composer.json?

It is possible to use frontend libraries with composer thanks to the
asset-packagist repository (https://asset-packagist.org/). If you 
installed commerce using the base package, you might already have this
setup.

For example, to use colorbox:
```
composer require npm-asset/colorbox:"^0.4"

```
Composer will detect new versions of the library that meet your constraints.
In the above example it will download anything from 0.4.* series of colorbox.

When managing libraries with composer this way, you may not want to add it to
version control. In that case, add specific directories to the .gitignore file.
```
# Specific libraries (which we manage with composer)
web/libraries/colorbox
```

For more details, see https://asset-packagist.org/site/about

#### Steps to install jquery print w/ asset-packagist and composer

1. Open composer.json file of your site.
2. Add the packagist repo to `repository`
```json   
        {
            "type": "composer",
            "url": "https://asset-packagist.org"
        }
```
2. Add the following to `require`
```json
    "oomphinc/composer-installers-extender": "^1.1"
```

3. Add to `"extra"`
```json
        "installer-types": [
            "bower-asset",
            "npm-asset"
        ],
```
4. Add to `"web/libraries"`
```json
                "type:bower-asset",
                "type:npm-asset"
```
5. Add `"DoersGuild/jQuery.print": "^1.5.1"` to the `"require"`.
```
"require": {
    .
    .
    "DoersGuild/jQuery.print": "master"
 }
```
6. Run `composer update DoersGuild/jQuery.print`.

### Manual method
1. Create `libraries` folder if it doesn't exists in the docroot of website.
2. `cd` into libraries folder and run 
`git clone https://github.com/DoersGuild/jQuery.print.git`.

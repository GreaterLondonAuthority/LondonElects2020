# Numiko Drupal 8 Profile #

This is the standard Numiko Drupal 8 profile including Media management (image & video) and paragraphs.

## What modules does this distribution contain? ##

This is a high-level view of the modules within, for a full list checkout the .info.yml file

* [Field Group](https://www.drupal.org/project/field_group)
* [Honey Pot](https://www.drupal.org/project/honeypot)
* [Media Entity](https://www.drupal.org/project/media_entity)
* [Entity Browser](https://www.drupal.org/project/entity_browser)
* [Meta tag](https://www.drupal.org/project/metatag)
* [Paragraphs](https://www.drupal.org/project/paragraphs)
* [Password Policy](https://www.drupal.org/project/password_policy)
* [Pathauto](https://www.drupal.org/project/pathauto)
* [Url Redirect](https://www.drupal.org/project/redirect)
* [Linkit](https://www.drupal.org/project/linkit)
* [Focal Point](https://www.drupal.org/project/focal_point)
* [Environment Indicator](https://www.drupal.org/project/environment_indicator)

## How do I get set up? ##

Installation using this distribution will import all modules required, plus all configuration of those modules.

This distribution is included as part of the [Drupal 8 Composer Template](https://bitbucket.org/numiko/drupal-8-composer-template) but can also be imported by cloning this repo or including it in your Drupal installation composer.json file.

```
composer require numiko/d8-profile:*
```

## Updating the install profile ##

In order to update the install profile

### Making your changes

* Create a fresh install from the profile (follow the instructions in the [Drupal 8 Composer Template](https://bitbucket.org/numiko/drupal-8-composer-template))
* Update that site with any config / module changes you want to make

### Updating the profile

* Clone a copy of the profile into a new directory and create a new release branch (ready to update)
* Ensure any composer.json dependencies added as part of the work above are added to the install profile (`sorry, this is a manual step`)
* Export the config from your install (`drush cex -y`)
* Copy all config files from your install to the profile

```
cd ~/PROJECT_ROOT
rm ~/INSTALL_PROFILE/config/install/*.yml
cp config/sync/*.yml ~/INSTALL_PROFILE/config/install/
# If there are dev / prod directories copy them all into config/install
cp config/dev/*.yml ~/INSTALL_PROFILE/config/install/
cp config/prod/*.yml ~/INSTALL_PROFILE/config/install/
```

* Remove all uuid references from the install profile

```
# Windows / Linux
find ~/INSTALL_PROFILE/config/install/ -type f -exec sed -i -e '/^uuid: /d' {} \;
find ~/INSTALL_PROFILE/config/install/ -type f -exec sed -i -e '/_core:/,+1d' {} \;
# Mac
find ~/INSTALL_PROFILE/config/install/ -type f -exec sed -i '' '/^uuid: /d' {} \;
find ~/INSTALL_PROFILE/config/install/ -type f -exec sed -i '' '/_core:/{N;d;}' {} \;
```

* Ensure any modules added are appended to the *d8_profile.info.yml* dependencies
* Remove the `config/install/core.extension.yml` file
* Commit
* Create a beta tag and push
* Now test.

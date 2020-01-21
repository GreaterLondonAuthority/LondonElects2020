# README #

Override Drupal's email validation service and add a check for a "." in the domain part of email addresses. This means that email addresses like name@domain are rejected (strictly valid, but only useful on intranets) and only sensible addresses like name@domain.tld are allowed.

## Usage ##

Just install the module. Now this email validation should work anywhere there is email validation.

## Unit tests ##

To run from docroot:

`../vendor/bin/phpunit modules/contrib/sensible_email_validation/ -c core/phpunit.xml.dist`
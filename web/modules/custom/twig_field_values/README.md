# Twig Field Values

Useful twig filters and functions reduce chains of filters in templates, long conditionals and ensure no double encoding. Works nicely with twig debugging so there are no trailing spaces or comments where we don't want them.

## Filters

# field

Designed to strip debugging comments and trim extra space. Pass a field straight to it.

`field_content|field` is equivalent to `field_content|render|striptags|trim`

# field_value

Get the value of a field, minus debugging comments. Uses the field filter internally, but converts to a string. Therefore this should only be used in comparisons and not output as `{{ field_content|field_value }}` as Drupal's auto-escaping will kick in, potentially double-encoding any entities.

# bool_value

Returns a boolean. Use for checkbox fields in comparisons.

# array_value

Returns individual items from a multi-value field as a simple array.

## Functions

# has_value(field)

Equivalent to `field_content|render|striptags|trim is not empty` but is neater and takes care of Twig debugging.

# any_has_value(fields)

Pass an array of fields, any true will be returned if any of them is not empty.  
  
`{% if any_has_value([content.field_telephone, content.field_email] ) %}`
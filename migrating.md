---
title: Migrating from version 1 to version 2
layout: default
nav_order: 4
---

# Migrating from version 1 to version 2

We've updated Collapsible Categories to version 2. This version improves the compatibility with 3rd party 
styles while also simplifying integration with 3rd party styles and extensions.

Note that styles and extensions already integrated with Collapsible Categories version 1 will no longer 
work as expected when Collapsible Categories version 2 is installed. So we recommend updating your 
styles or extensions.

## For Style Authors:

Collapsible Categories version 2 now uses a Font Awesome icon for the open and close buttons, eliminating 
the need to create them out of complex CSS rules. And now, with version 2, the only tweaks a style may need to make are to button positioning via margin rules and in some cases, button size via the font-size rule.

Please see the included 3rd party styles as a reference for how adjustments to the CSS are made to position 
the buttons in a way that looks good for the style. Note that many styles may no longer need any adjustment at all.

## For Extension Authors:

We've simplified how you can include the collapsible button into your extension. We have placed the button's 
code into a template file, all you need to do is include this file and assign your variables.

For example, this outdated code block from version 1:

```html
<a href="{U_FOO_COLLAPSE_URL}" 
    class="collapse-btn collapse-<!-- IF S_FOO_HIDDEN -->show<!-- ELSE -->hide<!-- ENDIF -->" 
    data-hidden="{S_FOO_HIDDEN}" 
    data-ajax="phpbb_collapse" 
    data-overlay="true" 
    title="{L_COLLAPSIBLE_CATEGORIES_TITLE}" 
    style="display: none; line-height: 0;"></a>
```

Should be updated to this for version 2:

{% raw %}
```twig
{% set S_CC_FORUM_HIDDEN = S_FOO_HIDDEN %}
{% set U_CC_COLLAPSE_URL = U_FOO_COLLAPSE_URL %}
{% include '@phpbb_collapsiblecategories/collapsible_categories_button.html' ignore missing %}
```
{% endraw %}

{: .note }
Notice that `S_FOO_HIDDEN` and `U_FOO_COLLAPSE_URL` are example variables. They may be named something different in your extension. See the [Adding Collapsible Categories to Extensions v2.x.x](v2xx.html) wiki for complete instructions on integrating Collapsible categories with your extension.

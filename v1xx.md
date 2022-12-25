---
title: Add Collapsible Categories v1 to Extensions
layout: default
nav_order: 3
---

# Add Collapsible Categories v1 to Extensions

{: .warning }
This page contains documentation for the discontinued 1.x.x versions of Collapsible Categories. Refer to the [V2](v2xx.html) documentation for the current version of Collapsible Categories. 

Adding Collapsible Forum Categories to an existing extension is easy for an extension author to achieve.

Collapsible Categories (CC) is based around the structure of phpBB Prosilver's forum category layout. That's the element with the `forabg` class and the content to be hidden inside the element with the `topiclist forums` classes.

This guide will use the Welcome on Index (WOI) extension by Stoker as an example.

It should be just as easy to modify any other applicable extension, such as MChat or Shoutbox, by referencing this guide. The main modifications are:
* [Add our template code to your template](#the-template-code)
* [Add our php code to populate the new template variables](#the-event-listener)
* [Update the services.yml with the new dependency variables](#the-servicesyml)

## The Template Code

CC's template events are designed for the `forumrow` loop and can not be used by WOI. WOI displays a single (non-looped) `forabg` element on the index page, so we need to hard code some template code into the WOI `index_body_mark_forums_before.html` file, loosely based on what is in CC's template events.

Add the following anchor point just before `<div class="forabg">` so you have:
```html
<a class="category<!-- IF S_FOO_HIDDEN --> hidden-category<!-- ENDIF -->" style="display: none; height: 0"></a>
<div class="forabg">
```

{: .note }
Notice that it has a custom variable `S_FOO_HIDDEN`. It can be renamed to whatever makes sense for your extension.

Next add the button code after the first closing `<dl>` tag, so you have something like:
```html
<dl class="icon">
	<dt><div class="list-inner" style="width:90%;">{L_WELCOME_TO_MOD} {SITENAME}</div></dt>
</dl>
<a href="{U_FOO_COLLAPSE_URL}" 
    class="collapse-btn collapse-<!-- IF S_FOO_HIDDEN -->show<!-- ELSE -->hide<!-- ENDIF -->" 
    data-hidden="{S_FOO_HIDDEN}" 
    data-ajax="phpbb_collapse" 
    data-overlay="true" 
    title="{L_COLLAPSIBLE_CATEGORIES_TITLE}" 
    style="display: none; line-height: 0;"></a>
```

{: .note }
Notice the custom variables `S_FOO_HIDDEN` and `U_FOO_COLLAPSE_URL` which will need to be set next.

If the element being collapsed does not have both the classes `topiclist forums` then you must add the class `collapsible` to the element that is being collapsed/expanded. For example, the MCHAT extension would be changed to:

```html
<div class="postbody mChatBodyFix collapsible">
```

## The Event Listener
Now we need to assign the two template variables `U_FOO_COLLAPSE_URL` and `S_FOO_HIDDEN`.

We can use WOI's `main()` function in the `event.php` class to assign these variables. We will also be needing the `$template` and CC's `$operator` objects, so they need to be added to the class constructor as dependencies (if not already there). The `$operator` object from CC will need to be optional, so it should be defined at the end of the arguments list with a default value of `null`.

The constructor should look like:

```php
public function __construct(
    \stoker\welcomeonindex\core\functions_welcomeonindex $functions,
    \phpbb\template\template $template,
    \phpbb\collapsiblecategories\operator\operator $operator = null
)
{
    $this->welcomeonindex_functions = $functions;
    $this->template = $template;
    $this->operator = $operator;
}
```

Now we can add the following code to WOI's `main()` function:

```php
if ($this->operator !== null)
{
    $fid = 'foo_1'; // can be any unique string to identify your extension's collapsible element
    $this->template->assign_vars(array(
        'S_FOO_HIDDEN'       => $this->operator->is_collapsed($fid),
        'U_FOO_COLLAPSE_URL' => $this->operator->get_collapsible_link($fid),
    ));
}
```

{: .note }
Notice that our code is wrapped in a conditional. This is very important and means that this code will only run if CC is installed and properly loaded.

## The services.yml
Finally, the `services.yml` needs to be updated with the additional dependencies we added to the event class constructor. Special note should be paid to the CC operator class, which is made optional by adding a `?` to it:

```yaml
stoker.welcomeonindex.listener:
    class: stoker\welcomeonindex\event\listener
    arguments:
        - '@stoker.welcomeonindex.functions'
        - '@template'
        - '@?phpbb.collapsiblecategories.operator'
    tags:
        - { name: 'event.listener' }
```

With just those few minor modifications, CC will be integrated into WOI.

---
title: Add Collapsible Categories v2 to Extensions
layout: default
nav_order: 2
---

# Add Collapsible Categories v2 to Extensions

Adding Collapsible Forum Categories to an existing extension is easy for an extension author to achieve.

Collapsible Categories is based around the structure of phpBB Prosilver's forum category layout. It appends its collapsing button to the HTML element with the `forabg` class and the content being hidden is inside the HTML element with the `topiclist forums` classes.

It should be easy to modify any applicable extension, such as MChat or Shoutbox, by referencing this guide. The main modifications are:

* [Add our template code to your template](#the-template-code)
* [Add our php code to define our template variables](#the-event-listener)
* [Add a unique language variable](#the-language-file)

## The Template Code

So you've got an extension with a Prosilver forum category container that you want to add Collapsible Categories to. 
The Collapsible Categories template events are designed for the `forumrow` loop and may not work with your extension. 
So we need to add some template code into your extension's template file.

Your extension's forum category HTML might look like:
```html
<div class="forabg">
  <div class="inner">
    <ul class="topiclist">
      <li class="header">
        <dl class="row-item">
          <dt>Some Category Title</dt>
        </dl>
      </li>
    </ul>
    <div class="topiclist forums">
      This is the content that will be collapsed/hidden
    </div>
  </div>
</div>
```

Add the following anchor point just before `<div class="forabg">`:

{% raw %}
```twig
<a class="category{% if S_DEMO_HIDDEN %} hidden-category{% endif %}" style="display: none; height: 0"></a>
```
{% endraw %}

{: .note }
Notice that it has a custom variable `S_DEMO_HIDDEN`. It can be renamed to whatever makes sense for your extension.

Next add the following button code after the first closing `</dl>` tag:

{% raw %}
```twig
{% set S_CC_FORUM_HIDDEN = S_DEMO_HIDDEN %}
{% set U_CC_COLLAPSE_URL = U_DEMO_COLLAPSE_URL %}
{% set L_CC_BUTTON_TITLE = 'DEMO_COLLAPSIBLE_TITLE' %}
{% include '@phpbb_collapsiblecategories/collapsible_categories_button.html' ignore missing %}
```
{% endraw %}

{: .important }
The use of `ignore missing` must be present to prevent problems with the rendering of your extension when Collapsible Categories is not installed.

If the element being collapsed does not have both the classes `topiclist forums` then you must add the class `collapsible` to the element that is being collapsed/expanded. For example, the MCHAT extension would look like:
	
```html
<div class="postbody mChatBodyFix collapsible">
```

Your updated forum category HTML code should now look like:

{% raw %}
```twig
<a class="category{% if S_DEMO_HIDDEN %} hidden-category{% endif %}" style="display: none; height: 0"></a>
<div class="forabg">
  <div class="inner">
    <ul class="topiclist">
      <li class="header">
        <dl class="row-item">
          <dt>Some Category Title</dt>
        </dl>
        {% set S_CC_FORUM_HIDDEN = S_DEMO_HIDDEN %}
        {% set U_CC_COLLAPSE_URL = U_DEMO_COLLAPSE_URL %}
        {% set L_CC_BUTTON_TITLE = 'DEMO_COLLAPSIBLE_TITLE' %}
        {% include '@phpbb_collapsiblecategories/collapsible_categories_button.html' ignore missing %}
      </li>
    </ul>
    <div class="topiclist forums collapsible">
      This is the content that will be collapsed/hidden
    </div>
  </div>
</div>
```
{% endraw %}

1. `S_DEMO_HIDDEN` - This should be uniquely named and defined in the Event listener as shown below.
2. `U_DEMO_COLLAPSE_URL` - This should be uniquely named and defined in the Event listener as shown below.
3. `DEMO_COLLAPSIBLE_TITLE` - This should be uniquely named and defined in a language file as shown below.

## The Event Listener
First we need to assign the two template variables `S_DEMO_HIDDEN` and `U_DEMO_COLLAPSE_URL`.

We can use your extension's event listener function (assuming there already is one for assigning variables and data to your template) to assign these variables.
We will be needing the `$template` and the Collapsible Categories `$cc_operator` objects, so they need to be added to the class constructor as dependencies (if not already there). 

{: .important }
The `$cc_operator` object will need to be optional, so it should be defined at the end of the arguments list with a default value of `null`.

The constructor would look like:

```php
use phpbb\template\template;
use phpbb\collapsiblecategories\operator\operator;

public function __construct(template $template, operator $cc_operator = null)
{
    $this->template = $template;
    $this->cc_operator = $cc_operator;
}
```

Now we can add the following template variable definitions to your main event listener function:

```php
if ($this->cc_operator !== null)
{
    $ccid = 'demo_1'; // can be any unique string to identify your extension's collapsible element
    $this->template->assign_vars([
        'S_DEMO_HIDDEN'       => $this->cc_operator->is_collapsed($ccid),
        'U_DEMO_COLLAPSE_URL' => $this->cc_operator->get_collapsible_link($ccid),
    ]);
}
```

{: .important }
Notice that our code is wrapped in a conditional. This ensures that this code will only run if Collapsible Categories is installed and enabled.

### The services.yml
The `services.yml` needs to be updated with the additional dependencies we added to the event listener's constructor:

```yaml 
acme.demo.listener:
    class: acme\demo\event\listener
    arguments:
        - '@template'
        - '@?phpbb.collapsiblecategories.operator'
    tags:
        - { name: 'event.listener' }
```

{: .important }
The Collapsible Categories operator class must be made optional by prepending a `?` to it.

## The Language File

Finally, the `DEMO_COLLAPSIBLE_TITLE` language variable needs to be defined in your extension's language file (remember to rename and change the variable and show/hide titles appropriately):

```php
// in your extensions language files
'DEMO_COLLAPSIBLE_TITLE' => [
	0 => 'Hide Demo’s category',
	1 => 'Show Demo’s category',
],
``` 

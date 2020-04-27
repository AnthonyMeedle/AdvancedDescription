# Advanced Description

Ajouter des descritpions personaliser, aletrner entre un titre, un texte puis une image a gauche du texte a droite suivi d'un texte a gauche une image a droite ... ou trois images puis a nouveau un texte ... 

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is AdvancedDescription.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require your-vendor/advanced-description-module:~1.0
```


## Hook

Dans outils Advanced Description, le but serait de pouvoir créer un texte avancé et le faire apparaitre sur le site via n'importe quel hook, mais pour le moment ça ne fonctionne pas a chaque fois, je ne sais pas si ça vient du cache a vider ou autre choses. 


## Loop

If your module declare one or more loop, describe them here like this :

### Input arguments

|Argument |Description |
|---      |--- |
|category | $category_id |
|product | $product_id |
|folder | $folder_id |
|content | $content_id |

### Output arguments

|Variable   |Description |
|---        |--- |
|$DESCRIPTION    | format html des variable (texte rentré) + structure imposée en base de donnée |

### Exemple


{loop type="advanceddescription.description" name="advdesc" category=$category_id}
    {$DESCRIPTION nofilter}
{/loop}

{loop type="advanceddescription.description" name="advdesc" product=$product_id}
    {$DESCRIPTION nofilter}
{/loop}

{loop type="advanceddescription.description" name="advdesc" folder=$folder_id}
    {$DESCRIPTION nofilter}
{/loop}

{loop type="advanceddescription.description" name="advdesc" content=$content_id}
    {$DESCRIPTION nofilter}
{/loop}


## Other ?

If you have other think to put, feel free to complete your readme as you want.

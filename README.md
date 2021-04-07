# 1. Minty Seed Library




## 1.1. Installation

Copy the extension to phpBB/ext/minty/seeds

NOTE: You will need to manually create the directory 'minty' within the ext directory then create a sub directory called 'seeds' within that, then copy in the contents of the extension. 

You should end up with the following directory structure:-
![/docs/images/install_location.png](./docs/images/install_location.png)



Go to "ACP" > "Customise" > "Extensions" and enable the "Minty Seed Library" extension.

![/docs/images/install_location.png](./docs/images/enable.png)

![/docs/images/install_location.png](./docs/images/confirm.png)

Once enabled you should see the extensions listed:-

![/docs/images/admin_ext_custom.png](./docs/images/admin_ext_custom.png)


Go to "ACP" > "Extensions" and enable the "Minty Seed Library" toolbar icon and configure the PHPBB table prefix etc.

There are a number of configuration options available with the ACP panel as follows:-

![/docs/images/admin_acp.png](./docs/images/admin_acp.png)

Note: the Database prefix needs to the prefix used for the tables generated during installed. This is usually 'phpbb_' unless customised.

# Usage:

## Un-Registered Users View

No icon is visible to to un-registered users and the extension is effectively disabled:-

![/docs/images/unregistered-view.png](./docs/images/unregistered-view.png)

## Registered Users View

Registered users will see the 'Seeds' icon in the toolbar:-
![/docs/images/registered-view.png](./docs/images/registered-view.png)

But will be given the option to disable via the user control panel if required:-

![/docs/images/noob_cpanel.png](./docs/images/noob_cpanel.png)

Other options such as the 'Grid Split' are also available on per user basis.
Grid split allows the seed name and breeder to remain visible when scrolling right to view the seed data:-

![/docs/images/noob_split.png](./docs/images/noob_split.png)

Basic users without any additional permissions will just see a read only version of the grid, and double clicking will show read only details for the record:-:- 

![/docs/images/noob_view.png](./docs/images/noob_view.png)

Or selecting the context menu by right clicking will show the limited options available to them:-

![/docs/images/noob_right_click.png](./docs/images/noob_right_click.png)



## Data Entry

Adding new seed records - with the correct permission you should see the following options:-

![/docs/images/admin_grid.png](./docs/images/admin_grid.png)

both above the grid and when right clicking for a context sensitive menu:-

![/docs/images/admin_menu.png](./docs/images/admin_menu.png)

Selecting new will allow the entry of a new seed record:-

![/docs/images/admin_new.png](./docs/images/admin_new.png)

Selecting edit, or double clicking a row with the appropriate permissions will show the dialog in edit mode:-

![/docs/images/admin_edit.png](./docs/images/admin_edit.png)


## Tags

Tags are my way of allowing easy entry of data while still trying to keep some consistency for searching, spelling, etc.

The idea is that you can type anything which will be accepted as valid but will be offered when others are typing a similar term etc. The tags can contain anything including spaces etc:-


![/docs/images/combo_create.png](./docs/images/combo_create.png)

Once you are finished typing, pressing 'TAB' will create the tag:-

![/docs/images/combe_created.png](./docs/images/combe_created.png)

and make this available to others to select as a shortcut:-

![/docs/images/combo_available.png](./docs/images/combo_available.png)

Already 'selected' tags for the current record will show as highlighted to the left side:-

![/docs/images/combo_tag_selected.png](./docs/images/combo_tag_selected.png)



## Genetics

Genetics is a slightly different tag in that the parent must already exist within the database to be able to select a valid entry:-

![/docs/images/combo_genetics.png](./docs/images/combo_genetics.png)

Manual entry of this field is limited to picking other seeds:-

![/docs/images/combo_genetics_selected.png](./docs/images/combo_genetics_selected.png)


![/docs/images/select_month.png](./docs/images/select_month.png)

## Breeder 

@todo

![/docs/images/add_breeder.png](./docs/images/add_breeder.png)


## Image Upload

Single image upload by drag dropping or browsing to a directory location:-

![/docs/images/image_upload.png](./docs/images/image_upload.png)

Complete folder can be dropped to add all images within the directory

![/docs/images/image_upload.png](./docs/images/drop_folders_of_images.png)

Images are uploading individually and can be removed before the record is saved.

![/docs/images/image_upload.png](./docs/images/multi-uploading.png)


## Points

Support has been integrated for [APS - Advanced Points System](https://github.com/phpBB-Studio/AdvancedPointsSystem) to allow forum points to be automatically granted when creating/editing records etc:-

![/docs/images/admin_aps_points.png](./docs/images/admin_aps_points.png)


## Permissions

Additional permissions can be granted on a per user basis to allow users to add records, edit records and delete records:-

![/docs/images/admin_user_permissions.png](./docs/images/admin_user_permissions.png)

These permissions can be extended for moderators to allow adding, editing and deleting of breeders:-

![/docs/images/admin_moderator_permissions.png](./docs/images/admin_moderator_permissions.png)

Admin permissions are automatically given to administrators but these can also be controlled:-

![/docs/images/admin_permissions.png](./docs/images/admin_permissions.png)


# 1.2. License

[GPLv2](license.txt)


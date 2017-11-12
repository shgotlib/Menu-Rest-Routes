# Rest nav menus

This plugin extends the WordPress JSON REST API with new routes for WordPress registered menus

The new routes available will be:

 * /wp-json/wp/v2/nav_menu/ - to get all registered menus with extra info:
   * their menu items.
   * each items can get it's icon from the "Menu icon" plugin (if installed).
   * each menu get the theme locations it registered them.
* /wp-json/wp/v2/nav_menu/\<menu_id\> - to get info about specific menu.


You can easily request data from your menus in Progressive Web Apps, for example in angular:

```javascript
// get only the main menu in some service
getPrimaryMenu() {
  this.http.get(YUOR_DOMAIN + "/wp-json/wp/v2/nav_menu/")
      .map(menu => return menu.theme_locations.indexOf("primary") > -1);
}

// get specific menu in a component
this.menuService.getMenuById(this.menuid)
	.subscribe(menu => this.menu = menu);
```

This plugin is extends [the main GitHub project](https://github.com/WP-API/WP-API) but it not part of it.

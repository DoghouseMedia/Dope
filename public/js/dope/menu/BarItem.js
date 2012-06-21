dojo.provide("dope.menu.BarItem");
dojo.require("dijit.MenuBarItem");
dojo.require("dope.menu.BarItem");

/**
 * BarItem is for menu items that are single buttons (no popup menu),
 * and must exist for CSS reasons. We also extend our usual Item class
 * in order to use its methods.
 */
dojo.declare('dope.menu.BarItem', [dijit.MenuBarItem, dope.menu.Item]);